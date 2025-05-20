// tailwind.config.js
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./public/**/*.{html,php}",
    "./.html"],
  theme: {
    screens: { // Esto SOBRESCRIBE los breakpoints por defecto
      xss: '320px',
      xs:  '480px',
      sm:  '640px',
      md:  '768px',
      lg:  '1024px',
      xl:  '1280px',
      '2xl':'1440px',
    },
    extend: { // Tus extensiones de colores y fuentes están bien aquí
      colors: {
        gov:  '#015dca',
        sena: '#39A900',
      },
      fontFamily: {
        sans: [
          '-apple-system',
          'BlinkMacSystemFont',
          '"Segoe UI"',
          'Roboto',
          'Helvetica',
          'Arial',
          'sans-serif',
        ],
      },
    },
  },
  darkMode: 'class',
  plugins: [require('tailwind-scrollbar')], // Asegúrate que este plugin es compatible con Tailwind v4 y ESM si hay problemas.
}