<?php
/**
 * los botones de la tienda siempre visibles
 */

/**
 * Cargar estilos y scripts del tema hijo (prioridad alta)
 */
function woodmart_child_enqueue_styles() {

wp_enqueue_style(
    'woodmart-style',
    get_template_directory_uri() . '/style.css',
    [],
    wp_get_theme('woodmart')->get('Version')
);

// 🧩 2. Cargar el CSS del tema hijo con control de versión dinámico
$child_style_path = get_stylesheet_directory() . '/style.css';
$child_style_version = file_exists($child_style_path) ? filemtime($child_style_path) : time();

wp_enqueue_style(
    'child-style',
    get_stylesheet_directory_uri() . '/style.css',
    ['woodmart-style'],
    $child_style_version // 👈 versión automática basada en fecha de modificación
);

    // 🧩 3. Cargar tus scripts personalizados
    wp_enqueue_script(
        'auto-scroll-categorias',
        get_stylesheet_directory_uri() . '/js/auto-scroll-categorias.js',
        [],
        filemtime(get_stylesheet_directory() . '/js/auto-scroll-categorias.js'),
        true
    );
     // 🧩 4. Cargar script del sticky inteligente
    wp_enqueue_script(
        'sticky-categorias',
        get_stylesheet_directory_uri() . '/js/sticky-categorias.js',
        [],
        filemtime(get_stylesheet_directory() . '/js/sticky-categorias.js'),
        true
    );

    // Script de carga dinámica de productos (solo móvil)
wp_enqueue_script(
  'ajax-categorias',
  get_stylesheet_directory_uri() . '/js/ajax-categorias.js',
  ['jquery'],
  filemtime(get_stylesheet_directory() . '/js/ajax-categorias.js'),
  true
);

    // (Opcional: si luego activas tu otro JS)
    // wp_enqueue_script(
    //   'custom-search-expand',
    //   get_stylesheet_directory_uri() . '/js/search-expand.js',
    //   [],
    //   filemtime(get_stylesheet_directory() . '/js/search-expand.js'),
    //   true
    // );
}
add_action('wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 1);

add_action('wp_enqueue_scripts', function () {
    // Font Awesome
    wp_enqueue_style(
        'fa',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
        [],
        '6.5.0'
    );
});

// Pintar el cintillo de WhatsApp en el footer
add_action('wp_footer', function () {
    ?>
    <div id="cta-bar" class="cta-bar" role="region" aria-label="Contacto por WhatsApp">
      <div class="cta-bar__content">
        <span class="cta-label"><b>Asesor de Ventas</b></span>

        <!-- Escritorio (WhatsApp Web) -->
        <div class="cta-whatsapp pc" aria-label="Contactos escritorio">
          <a class="boton_personalizado" href="https://web.whatsapp.com/send?phone=51961029863" target="_blank" rel="noopener">
            <i class="fab fa-whatsapp" aria-hidden="true"></i> <strong>Fiorela:</strong> <span>961 029 863</span>
          </a>
          <a class="boton_personalizado" href="https://web.whatsapp.com/send?phone=51940602652" target="_blank" rel="noopener">
            <i class="fab fa-whatsapp" aria-hidden="true"></i> <strong>Paul:</strong> <span>940 602 652</span>
          </a>
  <button type="button" class="cta-close" aria-label="Cerrar barra"> <i class="far fa-times-circle"></i></button>
      
        </div>

      </div>
    </div>

<script>
(function () {
  var bar = document.getElementById('cta-bar');
  if (!bar) return;

  var btn = bar.querySelector('.cta-close');
  if (btn) {
    btn.addEventListener('click', function () {
      bar.style.display = 'none'; 
    });
  }
})();
</script>

    <?php
});

// ============================================
// FUNCIONALIDAD CATÁLOGO CON WHATSAPP
// ============================================

// 1. OCULTAR PRECIOS EN TODOS LOS PRODUCTOS (FORZADO)
add_filter('woocommerce_get_price_html', 'hide_product_prices', 9999, 2);
function hide_product_prices($price, $product) {
    return ''; // Oculta el precio completamente
}

// Ocultar precios con CSS adicional por si acaso
add_action('wp_head', 'force_hide_prices_css');
function force_hide_prices_css() {
    ?>
    <style>
        .wrap-price,
        .price,
        .woocommerce-Price-amount,
        del.woocommerce-Price-amount,
        ins.woocommerce-Price-amount {
            display: none !important;
        }
    </style>
    <?php
}

// 2. AGREGAR BOTÓN "COMPRAR" EN PÁGINA DE PRODUCTO INDIVIDUAL
add_action('woocommerce_after_add_to_cart_button', 'add_whatsapp_buy_button_single');
function add_whatsapp_buy_button_single() {
    global $product;
    
    // Obtener información del producto
    $product_name = $product->get_name();
    $product_sku = $product->get_sku() ? $product->get_sku() : 'ID-' . $product->get_id();
    

    // Crear mensaje de WhatsApp (formato minimalista y UTF-8 seguro)
    $message  = "*Detalles del producto*\n";
    $message .= "-------------------------\n";
    $message .= "*Producto:* " . $product_name . "\n";
    $message .= "*Código:* " . $product_sku . "\n";
    $message .= "-------------------------\n";
    $message .= "Hola, quiero comprar este producto.";
    
    // Codificar mensaje
    $message_encoded = urlencode($message);
    
    // HTML del botón con JavaScript para selección aleatoria
    ?>
    <button type="button" class="single_add_to_cart_button button alt whatsapp-comprar-btn" style="background-color: #25D366; margin-left: 10px;">
        <i class="fab fa-whatsapp"></i> Comprar
    </button>
    
    <script>
    (function() {
        var btnComprar = document.querySelector('.whatsapp-comprar-btn');
        if (btnComprar && !btnComprar.hasAttribute('data-initialized')) {
            btnComprar.setAttribute('data-initialized', 'true');
            btnComprar.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Números de WhatsApp
                var numeros = ['51961029863', '51940602652'];
                
                // Selección aleatoria
                var numeroAleatorio = numeros[Math.floor(Math.random() * numeros.length)];
                
                // Crear URL de WhatsApp
                var mensaje = '<?php echo $message_encoded; ?>';
                var urlWhatsApp = 'https://wa.me/' + numeroAleatorio + '?text=' + mensaje;
                
                // Abrir WhatsApp
                window.open(urlWhatsApp, '_blank');
            });
        }
    })();
    </script>
    <?php
}

// ============================================
// SOLUCIÓN COMBINADA: BOTONES EN TODOS LOS LAYOUTS
// ============================================

// 3A. DETECTAR TIPO DE LAYOUT Y AGREGAR BOTONES APROPIADOS
add_action('woocommerce_after_shop_loop_item_title', 'add_whatsapp_buttons_smart', 15);
function add_whatsapp_buttons_smart() {
    global $product;
    
    // Obtener información del producto
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $product_sku = $product->get_sku() ? $product->get_sku() : 'ID-' . $product_id;
    
// Crear mensaje con formato tipo tabla (sin emojis)
$message  = "*Detalles del producto*\n";
$message .= "==============================\n\n";
$message .= "*Producto:*  " . $product_name . "\n";
$message .= "*Código:*    " . $product_sku . "\n";
$message .= "==============================\n";
$message .= "Hola, quiero comprar este producto.";

// Codificar correctamente para WhatsApp
$message_encoded = rawurlencode($message);
    
    // Detectar si estamos en categorías/shop (layout hover-icons)
    // vs carrusel/grid (layout con footer)
    $is_category_layout = is_shop() || is_product_category() || is_product_tag();
    
    if ($is_category_layout) {
        // LAYOUT HOVER-ICONS: Botones visibles después del título
        ?>
        <div class="wd-product-custom-buttons">
            <!-- Botón Añadir al Carrito -->
            <div>
                <a href="?add-to-cart=<?php echo $product_id; ?>" 
                   data-quantity="1" 
                   class="button product_type_simple add_to_cart_button ajax_add_to_cart add-to-cart-loop" 
                   data-product_id="<?php echo $product_id; ?>" 
                   data-product_sku="<?php echo esc_attr($product_sku); ?>" 
                   rel="nofollow">
                   <i class="fa-solid fa-cart-plus"></i>
                    <span>Carrito</span>
                </a>
            </div>
            
            <!-- Botón WhatsApp -->
            <div>
                <button type="button" 
                        class="button whatsapp-comprar-category-btn" 
                        data-product-id="<?php echo $product_id; ?>"
                        data-mensaje="<?php echo esc_attr($message_encoded); ?>">
                    <i class="fab fa-whatsapp"></i>
                    <span>Comprar</span>
                </button>
            </div>
        </div>
        <?php
    } else {
        // LAYOUT FOOTER/CARRUSEL: Usar JavaScript para inyectar en footer
        ?>
        <script>
        (function() {
            var productId = <?php echo $product_id; ?>;
            var mensaje = '<?php echo $message_encoded; ?>';
            
            function addWhatsAppButton() {
                var productElement = document.querySelector('[data-id="' + productId + '"]');
                if (!productElement) return false;
                
                // Buscar el footer del producto
                var footer = productElement.querySelector('.wd-product-footer');
                if (!footer) return false;
                
                var addToCartBtn = footer.querySelector('.wd-add-btn-replace');
                if (!addToCartBtn) return false;
                
                // Verificar si ya existe el botón de WhatsApp
                var existingWhatsAppBtn = productElement.querySelector('.wd-whatsapp-comprar-wrapper');
                if (existingWhatsAppBtn) return true;
                
                // Crear el botón de WhatsApp
                var whatsappWrapper = document.createElement('div');
                whatsappWrapper.className = 'wd-add-btn wd-add-btn-replace wd-whatsapp-comprar-wrapper';
                whatsappWrapper.innerHTML = '<button type="button" class="button alt whatsapp-comprar-loop" data-product-id="' + productId + '" data-mensaje="' + mensaje + '" style="background-color: #25D366;"><i class="fab fa-whatsapp"></i> <span>Comprar</span></button>';
                
                addToCartBtn.parentNode.insertBefore(whatsappWrapper, addToCartBtn.nextSibling);
                
                // Agregar evento click
                var whatsappBtn = whatsappWrapper.querySelector('.whatsapp-comprar-loop');
                if (whatsappBtn && !whatsappBtn.hasAttribute('data-initialized')) {
                    whatsappBtn.setAttribute('data-initialized', 'true');
                    whatsappBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        var numeros = ['51961029863', '51940602652'];
                        var numeroAleatorio = numeros[Math.floor(Math.random() * numeros.length)];
                        var urlWhatsApp = 'https://wa.me/' + numeroAleatorio + '?text=' + mensaje;
                        window.open(urlWhatsApp, '_blank');
                    });
                }
                
                return true;
            }
            
            // Intentar agregar inmediatamente
            if (!addWhatsAppButton()) {
                setTimeout(function() {
                    if (!addWhatsAppButton()) {
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', addWhatsAppButton);
                        }
                    }
                }, 100);
            }
            
            window.addEventListener('load', function() {
                addWhatsAppButton();
            });
        })();
        </script>
        <?php
    }
}

// 3B. OCULTAR LOS BOTONES ORIGINALES DE HOVER EN CATEGORÍAS
add_action('wp_head', 'hide_original_hover_buttons_in_categories');
function hide_original_hover_buttons_in_categories() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        ?>
        <style>
            /* Ocultar los botones originales de hover en categorías */
            .wd-hover-icons .wrapp-buttons,
            .wd-hover-icons .wd-buttons {
                display: none !important;
            }
        </style>
        <?php
    }
}

// 3C. SCRIPT GLOBAL PARA INICIALIZAR EVENTOS DE WHATSAPP
add_action('wp_footer', 'init_whatsapp_buttons_events', 10);
function init_whatsapp_buttons_events() {
    ?>
    <script>
    (function() {
        'use strict';
        
        function initCategoryButtons() {
            // Inicializar botones de categorías
            var buttons = document.querySelectorAll('.whatsapp-comprar-category-btn:not([data-initialized])');
            
            buttons.forEach(function(button) {
                button.setAttribute('data-initialized', 'true');
                
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var mensaje = this.getAttribute('data-mensaje');
                    var numeros = ['51961029863', '51940602652'];
                    var numeroAleatorio = numeros[Math.floor(Math.random() * numeros.length)];
                    var urlWhatsApp = 'https://wa.me/' + numeroAleatorio + '?text=' + mensaje;
                    
                    window.open(urlWhatsApp, '_blank');
                });
            });
        }
        
        function initFooterButtons() {
            // Inicializar botones de footer (ya tienen sus propios eventos inline)
            var buttons = document.querySelectorAll('.whatsapp-comprar-loop:not([data-initialized])');
            
            buttons.forEach(function(button) {
                if (!button.hasAttribute('data-initialized')) {
                    button.setAttribute('data-initialized', 'true');
                    
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        var mensaje = this.getAttribute('data-mensaje');
                        var numeros = ['51961029863', '51940602652'];
                        var numeroAleatorio = numeros[Math.floor(Math.random() * numeros.length)];
                        var urlWhatsApp = 'https://wa.me/' + numeroAleatorio + '?text=' + mensaje;
                        
                        window.open(urlWhatsApp, '_blank');
                    });
                }
            });
        }
        
        function initAll() {
            initCategoryButtons();
            initFooterButtons();
        }
        
        // Ejecutar al cargar
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAll);
        } else {
            initAll();
        }
        
        window.addEventListener('load', initAll);
        
        // Observador para contenido dinámico
        var observer = new MutationObserver(function(mutations) {
            var shouldInit = false;
            
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.classList && 
                            (node.classList.contains('wd-product') || 
                             node.classList.contains('whatsapp-comprar-category-btn') ||
                             node.classList.contains('whatsapp-comprar-loop') ||
                             node.querySelector('.wd-product'))) {
                            shouldInit = true;
                        }
                    }
                });
            });
            
            if (shouldInit) {
                setTimeout(initAll, 150);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Eventos de WooCommerce/Woodmart
        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('wdShopPageInit wdUpdateProducts yith_wcan_ajax_filtered updated_wc_div', function() {
                setTimeout(initAll, 200);
            });
        }
        
    })();
    </script>
    <?php
}

// 3D. CSS PARA AMBOS LAYOUTS
add_action('wp_head', 'whatsapp_buttons_combined_css', 9999);
function whatsapp_buttons_combined_css() {
    ?>
    <style>
        /* ========================================
           CSS PARA BOTONES EN CATEGORÍAS - CONTENIDOS
           ======================================== */
        
        /* Contenedor principal de botones */
        .wd-product-custom-buttons {
            opacity: 1 !important;
            visibility: visible !important;
            display: flex !important;
            gap: 8px !important;
            margin: 10px 0 0 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            align-items: stretch !important;
            box-sizing: border-box !important;
        }
        
        .wd-product .wd-product-custom-buttons,
        .wd-product:not(:hover) .wd-product-custom-buttons {
            opacity: 1 !important;
            visibility: visible !important;
            display: flex !important;
        }
        
        /* Contenedores individuales de cada botón */
        .wd-product-custom-buttons > div {
            flex: 1 1 50% !important;
            max-width: calc(50% - 4px) !important;
            min-width: 0 !important;
            display: flex !important;
            box-sizing: border-box !important;
        }
        
        /* Botón Añadir al Carrito */
        .wd-product-custom-buttons .add-to-cart-loop {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 10px 12px !important;
            min-height: 42px !important;
            background-color: #333 !important;
            color: white !important;
            text-decoration: none !important;
            border-radius: 4px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            line-height: 1.2 !important;
            transition: all 0.3s ease !important;
            border: none !important;
            box-sizing: border-box !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        .wd-product-custom-buttons .add-to-cart-loop:hover {
            background-color: #000 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
        }
        
        /* Botón WhatsApp */
        .whatsapp-comprar-category-btn {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            padding: 10px 12px !important;
            min-height: 42px !important;
            background-color: #25D366 !important;
            color: white !important;
            border: none !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            line-height: 1.2 !important;
            transition: all 0.3s ease !important;
            box-sizing: border-box !important;
            white-space: nowrap !important;
            overflow: hidden !important;
        }
        
        .whatsapp-comprar-category-btn:hover {
            background-color: #128C7E !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4) !important;
        }
        
        /* Textos de los botones */
        .wd-product-custom-buttons .add-to-cart-loop span,
        .whatsapp-comprar-category-btn span {
            font-size: 13px !important;
            line-height: 1.2 !important;
            display: inline-block !important;
        }
        
        /* Iconos */
        .whatsapp-comprar-category-btn i {
            font-size: 16px !important;
            flex-shrink: 0 !important;
        }
        
        /* Asegurar que el contenedor del producto no corte los botones */
        .wd-product .product-wrapper,
        .wd-product {
            overflow: visible !important;
        }
        
        /* Ajustar el espaciado con otros elementos */
        .wd-product .wd-product-cats {
            margin-bottom: 5px !important;
        }
        
        .wd-entities-title {
            margin-bottom: 8px !important;
        }
        
        /* ========================================
           CSS PARA BOTONES EN FOOTER (CARRUSELES)
           ======================================== */
        
        .wd-product .wd-product-footer,
        .wd-product:not(:hover) .wd-product-footer {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            transition: none !important;
            display: grid !important;
        }
        
        .wd-product-footer {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
            padding: 10px 0 !important;
        }
        
        .wd-product-footer .wd-add-btn-replace,
        .wd-product-footer .wd-whatsapp-comprar-wrapper {
            width: 100% !important;
            margin: 0 !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        .wd-product-footer .button {
            width: 100% !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .wd-product-footer .wd-action-buttons {
            grid-column: 1 / -1 !important;
            display: flex !important;
            justify-content: center !important;
            gap: 10px !important;
            margin-top: 5px !important;
        }
        
        /* ========================================
           ESTILOS COMUNES BOTÓN WHATSAPP
           ======================================== */
        
        .whatsapp-comprar-loop {
            background-color: #25D366 !important;
            color: white !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }
        
        .whatsapp-comprar-loop:hover {
            background-color: #128C7E !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4) !important;
        }
        
        /* Animación hover - ocultar texto */
        .whatsapp-comprar-category-btn i,
        .whatsapp-comprar-loop i {
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .whatsapp-comprar-category-btn span,
        .whatsapp-comprar-loop span {
            transition: opacity 0.3s ease, width 0.3s ease;
        }
        
        .whatsapp-comprar-category-btn:hover span,
        .whatsapp-comprar-loop:hover span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        
        .whatsapp-comprar-category-btn:hover i,
        .whatsapp-comprar-loop:hover i {
            transform: scale(1.3);
            animation: whatsapp-pulse 0.6s ease-in-out infinite;
        }
        
        @keyframes whatsapp-pulse {
            0%, 100% { transform: scale(1.3); }
            50% { transform: scale(1.5); }
        }
        
        /* ========================================
           RESPONSIVE
           ======================================== */
        
        @media (max-width: 768px) {
            
            .wd-product-footer {
                grid-template-columns: 1fr !important;
            }
        }

        /* ========================================
   📱 OPTIMIZACIÓN MÓVIL: BOTONES GRANDES Y FLUIDOS
   ======================================== */
@media (max-width: 768px) {

    /* 🔸 Contenedor principal: columna completa */
    .wd-product-custom-buttons {
        flex-direction: column !important;
        align-items: stretch !important;
        justify-content: center !important;
        gap: 12px !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 10px 0 !important;
        padding: 0 !important;
    }

    /* 🔸 Cada contenedor de botón ocupa todo el ancho */
    .wd-product-custom-buttons > div {
        flex: 1 1 100% !important;
        max-width: 100% !important;
        display: flex !important;
    }

    /* 🔸 Botones grandes, cómodos al tacto */
    .wd-product-custom-buttons .button,
    .whatsapp-comprar-category-btn,
    .add-to-cart-loop {
        width: 100% !important;
        font-size: 16px !important;
        padding: 15px 20px !important;
        border-radius: 30px !important;
        line-height: 1.3 !important;
        font-weight: 600 !important;
        justify-content: center !important;
        text-align: center !important;
        box-sizing: border-box !important;
    }

    /* 🔸 Íconos con tamaño proporcional */
    .wd-product-custom-buttons i {
        font-size: 18px !important;
    }

    /* 🔸 Animación y sombra más sutil en móvil */
    .wd-product-custom-buttons .button:hover {
        transform: none !important;
        box-shadow: 0 3px 8px rgba(0,0,0,0.15) !important;
    }

    /* 🔸 Footer del producto en carrusel: botones en columna */
    .wd-product-footer {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
    }
}

    </style>
    <?php
}

// 4. SCRIPT GLOBAL PARA REINICIALIZAR BOTONES EN AJAX Y CARRUSELES
add_action('wp_footer', 'whatsapp_global_reinit_script');
function whatsapp_global_reinit_script() {
    ?>
    <script>
    (function() {
        // Función global para inicializar todos los botones de WhatsApp
        window.initAllWhatsAppButtons = function() {
            var allProducts = document.querySelectorAll('.wd-product[data-id]');
            
            allProducts.forEach(function(productElement) {
                var productId = productElement.getAttribute('data-id');
                if (!productId) return;
                
                // Verificar si ya tiene el botón
                var existingWhatsAppBtn = productElement.querySelector('.wd-whatsapp-comprar-wrapper');
                if (existingWhatsAppBtn) return;
                
                // Buscar el botón Add to Cart
                var addToCartBtn = productElement.querySelector('.wd-add-btn-replace');
                if (!addToCartBtn) return;
                
                // Obtener mensaje del script inline si existe
                var scripts = productElement.parentElement.querySelectorAll('script');
                var mensaje = '';
                
                scripts.forEach(function(script) {
                    var content = script.textContent;
                    if (content.includes('var productId = ' + productId)) {
                        var match = content.match(/var mensaje = '([^']+)'/);
                        if (match) {
                            mensaje = match[1];
                        }
                    }
                });
                
                if (!mensaje) return;
                
                // Crear el botón
                var whatsappWrapper = document.createElement('div');
                whatsappWrapper.className = 'wd-add-btn wd-add-btn-replace wd-whatsapp-comprar-wrapper';
                whatsappWrapper.innerHTML = '<button type="button" class="button alt whatsapp-comprar-loop" data-product-id="' + productId + '" data-mensaje="' + mensaje + '" style="background-color: #25D366;"><i class="fab fa-whatsapp"></i> <span>Comprar</span></button>';
                
                // Insertar
                addToCartBtn.parentNode.insertBefore(whatsappWrapper, addToCartBtn.nextSibling);
                
                // Agregar evento
                var whatsappBtn = whatsappWrapper.querySelector('.whatsapp-comprar-loop');
                if (whatsappBtn && !whatsappBtn.hasAttribute('data-initialized')) {
                    whatsappBtn.setAttribute('data-initialized', 'true');
                    whatsappBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        var numeros = ['51961029863', '51940602652'];
                        var numeroAleatorio = numeros[Math.floor(Math.random() * numeros.length)];
                        var msg = this.getAttribute('data-mensaje');
                        var urlWhatsApp = 'https://wa.me/' + numeroAleatorio + '?text=' + msg;
                        window.open(urlWhatsApp, '_blank');
                    });
                }
            });
        };
        
        // Ejecutar al cargar
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', window.initAllWhatsAppButtons);
        } else {
            window.initAllWhatsAppButtons();
        }
        
        // Ejecutar después de carga completa
        window.addEventListener('load', window.initAllWhatsAppButtons);
        
        // Observer para detectar cambios en el DOM (carruseles, AJAX)
        var observer = new MutationObserver(function(mutations) {
            var shouldReinit = false;
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && (node.classList && (node.classList.contains('wd-product') || node.querySelector('.wd-product')))) {
                            shouldReinit = true;
                        }
                    });
                }
            });
            
            if (shouldReinit) {
                setTimeout(window.initAllWhatsAppButtons, 100);
            }
        });
        
        // Observar todo el body
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Reinicializar cada 2 segundos como fallback
        setInterval(window.initAllWhatsAppButtons, 2000);
    })();
    </script>
    <?php
}

// 5. BOTÓN DE COTIZAR EN EL CARRITO
add_action('woocommerce_after_cart_table', 'add_whatsapp_quote_button_to_cart');
function add_whatsapp_quote_button_to_cart() {
    $whatsapp_fiorela = '51961029863';
    $whatsapp_paul = '51940602652';
    
    // Obtener productos del carrito
    $cart_items = WC()->cart->get_cart();
    
    if (empty($cart_items)) {
        return;
    }
    
    // Crear mensaje con formato tipo tabla (sin iconos)
$message  = "*Cotización de productos*\n";
$message .= "====================================\n\n";

$total_items = 0;

foreach ($cart_items as $cart_item) {
    $product = $cart_item['data'];
    $product_name = $product->get_name();
    $product_sku = $product->get_sku() ? $product->get_sku() : 'ID-' . $product->get_id();
    $quantity = $cart_item['quantity'];
    $total_items += $quantity;

    // Simular tabla con texto plano
    $message .= "*Producto:*  {$product_name}\n";
    $message .= "*Código:*    {$product_sku}\n";
    $message .= "*Cantidad:*  {$quantity}\n";
    $message .= "------------------------------------\n";
}

$message .= "\n*Total de productos:* {$total_items}\n";
$message .= "¿Podrían enviarme una cotización detallada?";
    
    // Codificar mensaje
    $message_encoded = urlencode($message);
    $whatsapp_url_fiorela = "https://wa.me/{$whatsapp_fiorela}?text={$message_encoded}";
    $whatsapp_url_paul = "https://wa.me/{$whatsapp_paul}?text={$message_encoded}";
    
    // Mostrar botones
    echo '
    <div class="cart-whatsapp-section" style="margin: 30px 0; padding: 20px; background-color: #f8f8f8; border-radius: 8px; text-align: center;">
        <h3 style="margin-bottom: 15px; color: #333;">💬 Solicitar Cotización por WhatsApp</h3>
        <p style="margin-bottom: 20px; color: #666;">Envía tu lista de productos directamente a nuestros asesores</p>
        
        <div class="cart-whatsapp-buttons" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="' . $whatsapp_url_fiorela . '" target="_blank" class="button alt" style="background-color: #25D366; color: white; padding: 15px 30px; font-size: 16px; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; min-width: 200px; justify-content: center;">
                <i class="fab fa-whatsapp" style="font-size: 20px;"></i>
                <span><strong>Fiorela</strong><br><small>961 029 863</small></span>
            </a>
            <a href="' . $whatsapp_url_paul . '" target="_blank" class="button alt" style="background-color: #128C7E; color: white; padding: 15px 30px; font-size: 16px; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; min-width: 200px; justify-content: center;">
                <i class="fab fa-whatsapp" style="font-size: 20px;"></i>
                <span><strong>Paul</strong><br><small>940 602 652</small></span>
            </a>
        </div>
    </div>';
}

// 6. ESTILOS CSS MEJORADOS - BOTONES SIEMPRE VISIBLES
add_action('wp_head', 'custom_whatsapp_catalog_styles', 9999);
function custom_whatsapp_catalog_styles() {
    ?>
    <style>
        /* ========================================
           FORZAR VISIBILIDAD EN TODAS LAS PÁGINAS
           ======================================== */
        
        /* Forzar visibilidad del footer del producto SIEMPRE */
        .wd-product .wd-product-footer,
        .wd-product:not(:hover) .wd-product-footer,
        .wd-product:hover .wd-product-footer,
        .product-grid-item .wd-product-footer,
        .wd-carousel-item .wd-product-footer,
        .products .wd-product .wd-product-footer,
        .archive .wd-product-footer,
        .tax-product_cat .wd-product-footer,
        .post-type-archive-product .wd-product-footer,
        .wd-hover-buttons .wd-product-footer,
        .wd-hover-buttons-on-hover .wd-product-footer,
        .wd-hover-standard .wd-product-footer,
        .wd-hover-base .wd-product-footer {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            transition: none !important;
            display: grid !important;
            position: relative !important;
            pointer-events: auto !important;
            height: auto !important;
            overflow: visible !important;
            max-height: none !important;
        }
        
        /* Forzar visibilidad de TODOS los botones dentro del footer */
        .wd-product-footer .wd-add-btn,
        .wd-product-footer .wd-add-btn-replace,
        .wd-product-footer .wd-whatsapp-comprar-wrapper,
        .wd-product-footer .wd-action-buttons,
        .wd-product:not(:hover) .wd-add-btn,
        .wd-product:not(:hover) .wd-add-btn-replace,
        .wd-product:not(:hover) .wd-whatsapp-comprar-wrapper,
        .wd-product:not(:hover) .wd-action-buttons {
            opacity: 1 !important;
            visibility: visible !important;
            display: block !important;
            pointer-events: auto !important;
            position: relative !important;
            max-height: none !important;
        }
        
        /* Forzar visibilidad de botones y enlaces */
        .wd-product-footer .button,
        .wd-product-footer a.button,
        .wd-product-footer button,
        .wd-product:not(:hover) .button,
        .wd-product:not(:hover) a.button,
        .wd-product:not(:hover) button,
        .whatsapp-comprar-loop,
        .add-to-cart-loop {
            opacity: 1 !important;
            visibility: visible !important;
            display: flex !important;
            pointer-events: auto !important;
        }
        
        /* Eliminar transiciones que ocultan */
        .wd-hover-buttons-on-hover .product-element-bottom,
        .wd-hover-buttons .product-element-bottom,
        .wd-hover-standard .product-element-bottom,
        .wd-hover-base .product-element-bottom {
            transform: none !important;
            transition: none !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        /* Asegurar que los contenedores no oculten nada */
        .wd-product,
        .product-grid-item,
        .wd-carousel-item .wd-product,
        .product-wrapper {
            overflow: visible !important;
        }
        
        .product-element-bottom {
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
            transition: none !important;
            position: relative !important;
        }
        
        /* Eliminar máscaras overlay */
        .wd-product::before,
        .wd-product::after,
        .product-wrapper::before,
        .product-wrapper::after {
            content: none !important;
            display: none !important;
        }
        
        /* ========================================
           LAYOUT DE BOTONES LADO A LADO
           ======================================== */
        
        .wd-product-footer {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
            align-items: start !important;
            padding: 10px 0 !important;
        }
        
        .wd-add-btn-replace {
            width: 100% !important;
            margin: 0 !important;
        }
        
        .wd-whatsapp-comprar-wrapper {
            width: 100% !important;
            margin: 0 !important;
            display: block !important;
        }
        
        .wd-add-btn-replace .button,
        .wd-whatsapp-comprar-wrapper .button {
            width: 100% !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
        }
        
        .wd-action-buttons {
            grid-column: 1 / -1 !important;
            display: flex !important;
            justify-content: center !important;
            gap: 10px !important;
            margin-top: 5px !important;
        }
        
        /* ========================================
           ESTILOS BOTÓN WHATSAPP - LISTADOS
           ======================================== */
        
        .whatsapp-comprar-loop {
            cursor: pointer;
            width: 100%;
            position: relative;
            overflow: hidden;
            background-color: #25D366 !important;
            color: white !important;
            border: none !important;
            padding: 10px 16px !important;
            border-radius: 4px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }
        
        .whatsapp-comprar-loop:hover {
            background-color: #128C7E !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
        }
        
        .whatsapp-comprar-loop i {
            margin-right: 5px;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .whatsapp-comprar-loop span {
            display: inline;
            transition: opacity 0.3s ease;
        }
        
        .whatsapp-comprar-loop:hover span {
            opacity: 0;
            width: 0;
            margin: 0;
            padding: 0;
        }
        
        .whatsapp-comprar-loop:hover i {
            margin-right: 0;
            transform: scale(1.3);
            animation: whatsapp-pulse 0.6s ease-in-out infinite;
        }
        
        /* ========================================
           ESTILOS BOTÓN WHATSAPP - PÁGINA INDIVIDUAL
           ======================================== */
        
        .whatsapp-comprar-btn {
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .whatsapp-comprar-btn:hover {
            background-color: #128C7E !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
            transition: all 0.3s ease;
        }
        
        .whatsapp-comprar-btn i {
            margin-right: 5px;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .whatsapp-comprar-btn span {
            display: inline;
            transition: opacity 0.3s ease;
        }
        
        .whatsapp-comprar-btn:hover span {
            opacity: 0;
            width: 0;
            margin: 0;
            padding: 0;
        }
        
        .whatsapp-comprar-btn:hover i {
            margin-right: 0;
            transform: scale(1.3);
            animation: whatsapp-pulse 0.6s ease-in-out infinite;
        }
        
        @keyframes whatsapp-pulse {
            0%, 100% {
                transform: scale(1.3);
            }
            50% {
                transform: scale(1.5);
            }
        }
        
        /* ========================================
           BOTONES EN CARRITO
           ======================================== */
        
        .cart-whatsapp-buttons .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        /* ========================================
           RESPONSIVE MÓVILES
           ======================================== */
        
        @media (max-width: 768px) {
            .whatsapp-comprar-btn {
                width: 100%;
                margin-left: 0 !important;
                margin-top: 10px;
            }
            
            .wd-product-footer {
                grid-template-columns: 1fr !important;
            }
            
            .whatsapp-comprar-loop {
                width: 100%;
            }
            
            .cart-whatsapp-buttons {
                flex-direction: column;
            }
            
            .cart-whatsapp-buttons .button {
                width: 100%;
                min-width: auto !important;
            }
        }
    </style>
    <?php
}

// 7. SCRIPT PARA FORZAR VISIBILIDAD DE BOTONES
add_action('wp_footer', 'force_buttons_visibility_script', 9999);
function force_buttons_visibility_script() {
    ?>
    <script>
    (function() {
        'use strict';
        
        // Función para forzar visibilidad
        function forceAllButtonsVisible() {
            // Forzar visibilidad de footers
            var footers = document.querySelectorAll('.wd-product-footer');
            footers.forEach(function(footer) {
                footer.style.cssText = 'opacity: 1 !important; visibility: visible !important; display: grid !important; transform: translateY(0) !important;';
            });
            
            // Forzar visibilidad de wrappers
            var wrappers = document.querySelectorAll('.wd-whatsapp-comprar-wrapper, .wd-add-btn-replace');
            wrappers.forEach(function(wrapper) {
                wrapper.style.cssText = 'opacity: 1 !important; visibility: visible !important; display: block !important;';
            });
            
            // Forzar visibilidad de botones
            var buttons = document.querySelectorAll('.whatsapp-comprar-loop, .add-to-cart-loop, .wd-product-footer .button');
            buttons.forEach(function(button) {
                button.style.cssText = button.style.cssText + ' opacity: 1 !important; visibility: visible !important; display: flex !important;';
            });
            
            // Forzar visibilidad del contenedor bottom
            var bottomElements = document.querySelectorAll('.product-element-bottom');
            bottomElements.forEach(function(elem) {
                elem.style.cssText = 'opacity: 1 !important; visibility: visible !important; transform: none !important;';
            });
        }
        
        // Ejecutar inmediatamente
        forceAllButtonsVisible();
        
        // Ejecutar cuando DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', forceAllButtonsVisible);
        } else {
            forceAllButtonsVisible();
        }
        
        // Ejecutar en load
        window.addEventListener('load', forceAllButtonsVisible);
        
        // Ejecutar periódicamente los primeros 5 segundos
        var counter = 0;
        var interval = setInterval(function() {
            forceAllButtonsVisible();
            counter++;
            if (counter >= 10) {
                clearInterval(interval);
            }
        }, 500);
        
        // Observador de mutaciones
        var observer = new MutationObserver(function(mutations) {
            var shouldForce = false;
            
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.classList && 
                            (node.classList.contains('wd-product') || 
                             node.classList.contains('wd-product-footer') ||
                             node.querySelector('.wd-product') ||
                             node.querySelector('.wd-product-footer'))) {
                            shouldForce = true;
                        }
                    }
                });
            });
            
            if (shouldForce) {
                setTimeout(forceAllButtonsVisible, 100);
            }
        });
        
        // Observar todo el body
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style', 'class']
        });
        
        // Ejecutar después de eventos AJAX de WooCommerce
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(forceAllButtonsVisible);
            jQuery(document).on('yith_wcan_ajax_filtered updated_wc_div', forceAllButtonsVisible);
            jQuery(document.body).on('updated_cart_totals', forceAllButtonsVisible);
        }
        
    })();
    </script>

    
    <?php
}

// 7. REMOVER LOS BOTONES ORIGINALES DEL TEMA EN CATEGORÍAS
add_action('wp', 'remove_default_category_buttons', 20);
function remove_default_category_buttons() {
    // Solo en páginas de categorías y shop
    if (is_product_category() || is_shop() || is_product_tag() || is_product_taxonomy()) {
        // Remover el add to cart original del loop
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    }
}

// 8. modificacion de totalles del carrito en visualizar carrito 
function carrito_whatsapp_fixes() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
    // Función para aplicar todos nuestros cambios
    function aplicarCambiosCarrito() {
        // Ocultar elementos no deseados
        $('.wd-page-title.page-title-default').hide();
        $('th.product-price, td.product-price, th.product-subtotal, td.product-subtotal').hide();
        $('tr.wd-cart-action-row').hide();
        $('.cart_totals .cart-totals-inner h2, .cart_totals .cart-totals-inner table, .cart_totals .cart-totals-inner .wc-proceed-to-checkout').hide();
        
        // Mover sección WhatsApp a los totales si existe
        if ($('.cart-whatsapp-section').length && !$('.cart_totals .cart-whatsapp-section').length) {
            $('.cart_totals .cart-totals-inner').append($('.cart-whatsapp-section').html());
            $('.cart-whatsapp-section').remove();
        }
        
        // Aplicar estilos a botones WhatsApp
        $('.cart-whatsapp-buttons a i.fa-whatsapp').hide();
        $('.cart-whatsapp-buttons').css({
            'display': 'flex',
            'flex-wrap': 'nowrap',
            'gap': '15px',
            'justify-content': 'center'
        });
    }
    
    // Aplicar cambios al cargar la página
    aplicarCambiosCarrito();
    
    // Re-aplicar cambios después de cualquier actualización del carrito
    $(document).ajaxComplete(function() {
        setTimeout(aplicarCambiosCarrito, 100);
    });
    
    // También re-aplicar cuando se hace submit del formulario
    $('.woocommerce-cart-form').on('submit', function() {
        setTimeout(aplicarCambiosCarrito, 500);
    });
});
    </script>
    <?php
}
add_action('wp_footer', 'carrito_whatsapp_fixes');

// ======================================================
// 🧩 REINYECCIÓN DEL CSS DEL TEMA HIJO DESPUÉS DE CARGAS AJAX (WOODMART + WOOCOMMERCE + YITH)
// ======================================================
add_action('wp_footer', function () {
    ?>
    <script>
    (function() {
        'use strict';

        const events = [
            'woodmart-ajax-content-reloaded',
            'wdShopPageInit',
            'wdUpdateProducts',
            'yith_wcan_ajax_filtered',
            'updated_wc_div',
            'woodmart-quick-view-displayed',
            'woodmart-quick-view-hidden'
        ];

        const hrefBase = '<?php echo get_stylesheet_directory_uri(); ?>/style.css';

function reinjectChildCSS() {
    const preload = document.querySelector('link[rel="preload"][href*="style.css"]');
    if (preload) {
        preload.setAttribute('rel', 'stylesheet');
        preload.setAttribute('as', '');
        preload.href = preload.href.split('?')[0] + '?v=' + Date.now();
        console.log('%c[Child Theme] CSS actualizado usando preload','color:#22c55e;font-weight:bold;');
        return;
    }

    // Si no existe preload, crear uno nuevo
    const existing = document.getElementById('woodmart-child-style');
    if (existing) existing.remove();
    const link = document.createElement('link');
    link.id = 'woodmart-child-style';
    link.rel = 'stylesheet';
    link.href = '<?php echo get_stylesheet_directory_uri(); ?>/style.css?v=' + Date.now();
    document.head.appendChild(link);
}


        // Ejecutar al cargar y en cada evento AJAX
        document.addEventListener('DOMContentLoaded', reinjectChildCSS);
        window.addEventListener('load', reinjectChildCSS);
        events.forEach(e => document.addEventListener(e, reinjectChildCSS));
    })();
    </script>
    <?php
});


// 🚫 Evitar que el tema precargue el CSS del tema hijo (evita warning de preload)
add_action('wp_head', function() {
    ob_start(function($html) {
        // Elimina líneas con preload hacia style.css del child theme
        return preg_replace(
            '#<link[^>]+rel=["\']preload["\'][^>]+style\.css[^>]*>#i',
            '',
            $html
        );
    });
}, 0);


/**
 * =========================================================
 * ⚙️ Slider dinámico de categorías — solo móvil
 * =========================================================
 */
add_action('woocommerce_before_main_content', function () {
    if (is_product_category()) {
        ?>
        <div class="categoriasGeneralesMovile categoria-slider-movil" id="slider-categorias-dinamico">
            <div class="text-center wd-nav-product-cat-wrap">
                <ul class="wd-nav-product-cat wd-active wd-nav wd-gap-m wd-style-underline hasno-product-count">
                    <?php
                    // Obtener todas las categorías de productos visibles
                    $terms = get_terms([
                        'taxonomy' => 'product_cat',
                        'hide_empty' => false,
                        'parent' => 0, // Solo categorías principales
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ]);

                    if (!empty($terms) && !is_wp_error($terms)) {
                        foreach ($terms as $term) {
                            $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
                            $image_url = wp_get_attachment_url($thumbnail_id);
                            if (!$image_url) {
                                // Imagen de respaldo si no tiene miniatura asignada
                                $image_url = get_stylesheet_directory_uri() . '/img/default-cat.png';
                            }

                            // Clase "active" si estamos dentro de esta categoría
                            $active_class = (is_product_category($term->slug)) ? 'active' : '';
                            ?>
                            <li class="cat-item cat-item-<?php echo esc_attr($term->term_id); ?>">
                                <a class="category-nav-link <?php echo $active_class; ?>" 
                                   href="<?php echo esc_url(get_term_link($term)); ?>">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term->name); ?>" class="wd-nav-img" loading="lazy">
                                    <span class="nav-link-summary"><span class="nav-link-text"><?php echo esc_html($term->name); ?></span></span>
                                </a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <?php
    }
}, 5);


// ======================================================
// 💬 BOTÓN FLOTANTE WHATSAPP + POPUP DE ASESORES (SOLO MÓVIL)
// ======================================================
add_action('wp_footer', function () { ?>
    <!-- Botón flotante WhatsApp (solo móvil) -->
    <div id="whatsapp-float" class="whatsapp-float" aria-label="Abrir asesores">
        <i class="fab fa-whatsapp"></i>
    </div>

    <!-- Popup asesores (solo móvil) -->
    <div id="popup-whatsapp" class="popup-whatsapp" role="dialog" aria-label="Asesores de Ventas">
        <div class="popup-content">
            <button type="button" class="popup-close" aria-label="Cerrar ventana"><i class="far fa-times-circle"></i></button>
            <h3>Asesores de Ventas</h3>
            <a href="https://api.whatsapp.com/send?phone=51961029863" target="_blank" rel="noopener" class="popup-btn">
                <i class="fab fa-whatsapp"></i> Fiorela
            </a>
            <a href="https://api.whatsapp.com/send?phone=51940602652" target="_blank" rel="noopener" class="popup-btn">
                <i class="fab fa-whatsapp"></i> Paul
            </a>
        </div>
    </div>

    <style>
    /* Ocultar en escritorio */
    @media (min-width: 769px) {
        #whatsapp-float,
        #popup-whatsapp {
            display: none !important;
        }
    }

    /* Mostrar en móviles */
    @media (max-width: 768px) {
        .whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #25D366;
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 10000;
        }

        .popup-whatsapp {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10001;
        }

        .popup-whatsapp.active {
            display: flex;
        }

        .popup-content {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            width: 90%;
            max-width: 320px;
            text-align: center;
            position: relative;
            animation: slideUp 0.3s ease;
        }

        .popup-content h3 {
            margin-bottom: 15px;
            color: #333;
            font-weight: 700;
        }

        .popup-btn {
            display: block;
            background: #25D366;
            color: #fff;
            margin: 8px 0;
            padding: 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .popup-btn:hover {
            background: #128C7E;
            transform: translateY(-2px);
        }

        .popup-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            color: #333;
            cursor: pointer;
        }

        @keyframes slideUp {
            from {opacity: 0; transform: translateY(40px);}
            to {opacity: 1; transform: translateY(0);}
        }
    }
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const popup = document.getElementById("popup-whatsapp");
        const btnFloat = document.getElementById("whatsapp-float");
        const closePopup = popup.querySelector(".popup-close");

        // Mostrar popup solo si está visible (móvil)
        btnFloat.addEventListener("click", () => {
            if (window.innerWidth <= 768) popup.classList.add("active");
        });

        closePopup.addEventListener("click", () => popup.classList.remove("active"));
    });
    </script>
<?php });

add_action('wp_head', function() {
    echo '<link rel="preload" href="' . get_stylesheet_directory_uri() . '/style.css" as="style">';
}, 1);

add_filter('style_loader_tag', function($tag, $handle) {
    if ($handle === 'child-style') {
        $tag = str_replace("media='all'", "media='all' data-priority='high'", $tag);
    }
    return $tag;
}, 10, 2);