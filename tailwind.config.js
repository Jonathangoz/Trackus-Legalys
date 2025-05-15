// tailwind.config.js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.{html,php,js,jsx,ts,tsx,vue}"
  ],
  theme: {
    extend: {
      colors: {
        gov: '#015dca',
        sena: '#39A900',
      },
    },
  },
  darkMode: 'class',
  plugins: [
    require('tailwind-scrollbar'),
  ],
}
