document.addEventListener('DOMContentLoaded', () => {
  // Referencias a elementos principales
  const tabs = document.querySelectorAll('.tab-button');
  const tabContents = document.querySelectorAll('.tab-content');

  // 1. Funcionalidad de tabs
  tabs.forEach(button => {
    button.addEventListener('click', () => {
      // Quitar clases activas
      tabs.forEach(btn => btn.classList.remove('tab-button--active'));
      tabContents.forEach(content => content.classList.remove('tab-content--active'));

      // Activar la pestaña clickeada
      button.classList.add('tab-button--active');
      const tabName = button.getAttribute('data-tab');      // p.e. "registro"
      const section = document.getElementById(tabName);     // busca <section id="registro">
      if (section) {
        section.classList.add('tab-content--active');
      }
    });
  });
});