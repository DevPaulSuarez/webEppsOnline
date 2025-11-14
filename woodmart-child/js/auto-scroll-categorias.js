// ======================================================
// 🧩 AUTO / MANUAL SCROLL SEGÚN DISPOSITIVO
// ======================================================
document.addEventListener('DOMContentLoaded', () => {
  const desktopContainer = document.querySelector('.categoriasGenerales .wd-nav-product-cat');
  const mobileContainer = document.querySelector('.categoriasGeneralesMovile .wd-nav-product-cat');

  const isMobile = window.matchMedia('(max-width: 768px)').matches;

  // ======================================================
  // 📱 MODO MÓVIL — Scroll manual táctil
  // ======================================================
  if (isMobile && mobileContainer) {
 

    let isDown = false;
    let startX;
    let scrollLeft;

    // 🖱️ soporte para arrastre manual (solo si hay mouse)
    mobileContainer.addEventListener('mousedown', (e) => {
      isDown = true;
      mobileContainer.classList.add('active');
      startX = e.pageX - mobileContainer.offsetLeft;
      scrollLeft = mobileContainer.scrollLeft;
    });
    mobileContainer.addEventListener('mouseleave', () => {
      isDown = false;
      mobileContainer.classList.remove('active');
    });
    mobileContainer.addEventListener('mouseup', () => {
      isDown = false;
      mobileContainer.classList.remove('active');
    });
    mobileContainer.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - mobileContainer.offsetLeft;
      const walk = (x - startX) * 1.5;
      mobileContainer.scrollLeft = scrollLeft - walk;
    });

    // No hay animación automática en móvil
    return;
  }

  // ======================================================
  // 🖥️ MODO DESKTOP — Auto scroll reversible
  // ======================================================
  if (desktopContainer) {
    let scrollStep = 0.5;
    let currentStep = scrollStep;
    let direction = 1;
    let easing = 0.05;
    let targetStep = scrollStep;
    let reversing = false;
    let isVisible = true;

    function animateScroll() {
      if (!isVisible || !desktopContainer.isConnected) return;

      if (reversing) {
        currentStep += (0 - currentStep) * easing;
        if (currentStep < 0.01) {
          direction *= -1;
          reversing = false;
          targetStep = scrollStep;
        }
      } else {
        currentStep += (targetStep - currentStep) * easing;
      }

      desktopContainer.scrollLeft += currentStep * direction;
      const maxScrollLeft = desktopContainer.scrollWidth - desktopContainer.clientWidth;

      if (!reversing) {
        if (direction === 1 && desktopContainer.scrollLeft >= maxScrollLeft - 1) {
          reversing = true;
        } else if (direction === -1 && desktopContainer.scrollLeft <= 1) {
          reversing = true;
        }
      }

      requestAnimationFrame(animateScroll);
    }

    // 👁️ Pausar si no está visible
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        isVisible = entry.isIntersecting;
        if (isVisible) requestAnimationFrame(animateScroll);
      });
    });
    observer.observe(desktopContainer);

    // 🖱️ Pausar con hover
    desktopContainer.addEventListener('mouseenter', () => targetStep = 0);
    desktopContainer.addEventListener('mouseleave', () => targetStep = scrollStep);

    // Iniciar animación
    requestAnimationFrame(animateScroll);
  }

  
});
