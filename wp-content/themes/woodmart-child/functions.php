<?php
/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), woodmart_get_theme_info( 'Version' ) );
	wp_enqueue_script('custom-search-expand', get_stylesheet_directory_uri() . '/js/search-expand.js', array(), null, true);
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );
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
          <a class="boton_personalizado" href="https://web.whatsapp.com/send?phone=51981309331&text=Tiendas%20Tec" target="_blank" rel="noopener">
            <i class="fab fa-whatsapp" aria-hidden="true"></i> <strong>Fiorela:</strong> <span>981 309 331</span>
          </a>
          <a class="boton_personalizado" href="https://web.whatsapp.com/send?phone=51972640909&text=Tiendas%20Tec" target="_blank" rel="noopener">
            <i class="fab fa-whatsapp" aria-hidden="true"></i> <strong>Paul:</strong> <span>972 640 909</span>
          </a>

        </div>
        <!-- Móvil (abre app) -->
        <div class="cta-whatsapp mobile" aria-label="Contactos móvil">
          <a class="boton_personalizado_movile" href="https://api.whatsapp.com/send?phone=51981309331&text=Hola%20Tiendas%20Tec" target="_blank" rel="noopener">
            <strong>Fiorela:</strong> <span>981 309 331</span> <i class="fab fa-whatsapp" aria-hidden="true"></i>
          </a>
          <a class="boton_personalizado_movile" href="https://api.whatsapp.com/send?phone=51972640909&text=Hola%20Tiendas%20Tec" target="_blank" rel="noopener">
            <strong>Paul:</strong> <span>972 640 909</span> <i class="fab fa-whatsapp" aria-hidden="true"></i>
          </a>

        </div>
        <button type="button" class="cta-close" aria-label="Cerrar barra"> <i class="far fa-times-circle"></i></button>
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


