// tailwind.config.js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.{html,php,js,jsx,ts,tsx,vue}",
  ],
  theme: {
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
    extend: {
      colors: {
        'gov': '#015dca',
        'sena': '#39A900',
      },
    },
  },
  darkMode: 'class',
  plugins: [
    require('tailwind-scrollbar'),
  ],
};
