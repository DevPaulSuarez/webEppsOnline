document.addEventListener("DOMContentLoaded", () => {
  const barra = document.querySelector(".categoriasGeneralesMovile");
  if (!barra) return;

  const isMobile = window.matchMedia("(max-width: 768px)").matches;
  if (!isMobile) return;

  // Detectar el header principal de Woodmart
  const header =
    document.querySelector(".whb-general-header-inner") ||
    document.querySelector(".whb-sticked") ||
    document.querySelector("header");

  if (!header) return;

  // Calcular altura del header en tiempo real
  let headerHeight = header.offsetHeight;

  // Mantener la altura actualizada (cuando el header cambia al modo sticky)
  const observer = new ResizeObserver(() => {
    headerHeight = header.offsetHeight;
    if (isFixed) barra.style.top = `${headerHeight}px`;
  });
  observer.observe(header);

  // Crear separador para evitar salto del contenido
  const spacer = document.createElement("div");
  spacer.style.height = `${barra.offsetHeight}px`;
  barra.after(spacer);

  let isFixed = false;

  function fixBar() {
    if (isFixed) return;
    barra.style.position = "fixed";
    barra.style.top = `${headerHeight}px`;
    barra.style.left = "0";
    barra.style.right = "0";
    barra.style.width = "100%";
    barra.style.zIndex = "9980";
    barra.style.backgroundColor = "#f59d0c";
    barra.style.transition = "box-shadow 0.3s ease";
    barra.style.boxShadow = "0 3px 10px rgba(0, 0, 0, 0.1)";
    isFixed = true;
  }

  function unfixBar() {
    if (!isFixed) return;
    barra.removeAttribute("style");
    isFixed = false;
  }

  window.addEventListener("scroll", () => {
    const scrollTop = window.scrollY;

    // Se fija cuando el usuario baja más que la altura del header
    if (scrollTop > headerHeight) {
      fixBar();
    } else {
      unfixBar();
    }
  });
});
