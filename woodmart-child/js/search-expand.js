document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('.categoriasGenerales .wd-nav-product-cat');
  if (!container) return;

  let scrollStep = 0.5;          // velocidad máxima
  let currentStep = scrollStep;  // velocidad actual (se ajusta con easing)
  let direction = 1;             // 1 = derecha, -1 = izquierda
  let easing = 0.05;             // factor de suavizado (0-1)
  let targetStep = scrollStep;   // velocidad objetivo
  let reversing = false;         // control para evitar múltiples timeouts

  function animateScroll() {
    // Si estamos en proceso de reversa, reducimos suavemente la velocidad a 0
    if (reversing) {
      currentStep += (0 - currentStep) * easing;
      if (currentStep < 0.01) { // casi detenido
        direction *= -1;       // invertimos la dirección
        reversing = false;     // terminamos la reversa
        targetStep = scrollStep; // restauramos la velocidad objetivo
      }
    } else {
      // Si no estamos en reversa, aceleramos suavemente hacia targetStep
      currentStep += (targetStep - currentStep) * easing;
    }

    container.scrollLeft += currentStep * direction;

    const maxScrollLeft = container.scrollWidth - container.clientWidth;

    if (!reversing) {
      if (direction === 1 && container.scrollLeft >= maxScrollLeft - 1) {
        reversing = true;
      } else if (direction === -1 && container.scrollLeft <= 1) {
        reversing = true;
      }
    }

    requestAnimationFrame(animateScroll);
  }

  animateScroll();

  // Pausar al pasar mouse
  container.addEventListener('mouseenter', () => targetStep = 0);
  container.addEventListener('mouseleave', () => targetStep = scrollStep);
});
