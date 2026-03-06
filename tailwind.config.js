/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class', // Klass orqali dark modeni boshqarish uchun qoldiramiz
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}