document.addEventListener("DOMContentLoaded", () => {
  // 📱 Solo ejecutar en móviles
  const isMobile = window.matchMedia("(max-width: 768px)").matches;
  if (!isMobile) return;

  // 📦 Contenedor de categorías y productos
  const categorias = document.querySelectorAll(".categoriasGeneralesMovile .category-nav-link");
  const productosContainer = document.querySelector(".products.wd-products");
  if (!categorias.length || !productosContainer) return;

  // 🔄 Crear loader animado
  const loader = document.createElement("div");
  loader.className = "ajax-loader";
  loader.innerHTML = `<div class="spinner"></div>`;
  loader.style.display = "none";
  productosContainer.parentNode.insertBefore(loader, productosContainer);

  // 🚀 Reemplazo dinámico de productos
  categorias.forEach((link) => {
    link.addEventListener("click", async (e) => {
      e.preventDefault();

      const url = link.getAttribute("href");
      if (!url) return;

      // Mostrar loader
      loader.style.display = "flex";
      productosContainer.style.opacity = "0.3";
      productosContainer.style.pointerEvents = "none";

      try {
        // Cargar HTML de la categoría seleccionada
        const response = await fetch(url);
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, "text/html");

        const nuevosProductos = doc.querySelector(".products.wd-products");

        if (nuevosProductos) {
          productosContainer.innerHTML = nuevosProductos.innerHTML;

          // ✅ Reactivar scripts del tema Woodmart
          if (typeof woodmartThemeModule !== "undefined") {
            woodmartThemeModule.$document.trigger("wdShopPageInit");
          }

          // ♻️ Reinyectar scripts inline (botones WhatsApp, etc.)
          const inlineScripts = nuevosProductos.querySelectorAll("script");
          inlineScripts.forEach((oldScript) => {
            const newScript = document.createElement("script");
            newScript.text = oldScript.textContent;
            document.body.appendChild(newScript);
          });
        }

        // 🎨 Actualizar categoría activa visualmente
        categorias.forEach((el) => el.classList.remove("active"));
        link.classList.add("active");

        // 📍Actualizar URL sin recargar
        window.history.pushState({}, "", url);

        // 🧭 Scroll hacia arriba para ver los productos nuevos
        productosContainer.scrollIntoView({ behavior: "smooth", block: "start" });
      } catch (error) {
        console.error("❌ Error al cargar productos vía AJAX:", error);
      } finally {
        loader.style.display = "none";
        productosContainer.style.opacity = "1";
        productosContainer.style.pointerEvents = "auto";
      }
    });
  });

  // 🔙 Soporte para botón “atrás” del navegador
  window.addEventListener("popstate", async () => {
    const response = await fetch(window.location.href);
    const html = await response.text();
    const doc = new DOMParser().parseFromString(html, "text/html");
    const nuevosProductos = doc.querySelector(".products.wd-products");
    if (nuevosProductos) {
      productosContainer.innerHTML = nuevosProductos.innerHTML;
      if (typeof woodmartThemeModule !== "undefined") {
        woodmartThemeModule.$document.trigger("wdShopPageInit");
      }
    }
  });
});
