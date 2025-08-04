// tailwind.config.js
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./public/**/*.{html,php}",
    "./.html"],
  theme: {
    extend: { 
      screens: { // Esto SOBRESCRIBE los breakpoints por defecto
        xss: '320px',
        xs:  '480px',
        xsm: '520px',
        sm:  '600px',
        md:  '768px',
        lg:  '1024px',
        xl:  '1280px',
        '2xl':'1440px',
        '3xl':'1600px',
        '4xl':'1920px',
        '5xl':'2040px',
        '6xl':'2500px',
        '7xl':'3800px',
      },  
      // extensiones de colores y fuentes
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