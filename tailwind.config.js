/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{html,js,php}", // Incluye todos los archivos HTML y JS dentro de src
    "./index.html", // Incluye el archivo index.html fuera de src
    "./*.html",
  ],

  theme: {
    extend: {},
  },
  plugins: [],
};
