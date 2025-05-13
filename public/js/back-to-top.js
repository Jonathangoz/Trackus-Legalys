//Back to Top
// Mostrar el botón cuando el usuario se desplaza hacia abajo
window.onscroll = function () {
    const btn = document.getElementById("backToTopBtn");
    if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
      btn.style.display = "block";
    } else {
      btn.style.display = "none";
    }
  };
  
  // Volver arriba cuando se hace clic
  document.getElementById("backToTopBtn").addEventListener("click", function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });  