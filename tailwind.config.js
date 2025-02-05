/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './vendor/spatie/laravel-support-bubble/config/**/*.php',
    './vendor/spatie/laravel-support-bubble/resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Poppins"', 'sans-serif'],
      }
    },
  },
  plugins: [],
}

