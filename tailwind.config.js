/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig',
    "./assets/**/*.js",
    './node_modules/tw-elements/dist/js/**/*.js'
  ],
  theme: {
    fontFamily: {
      'menu-item': ['Raleway'],
      'title': ['Rokkitt'],
    },
    extend: {
      keyframes: {
        menu: {
          '0%': {
            transform: 'translateY(-25%)',
          },
          '100%': {
            transform: 'translateY(0)',
          },
        }
      },
      animation: {
        menu: 'menu 0.2s ease-in-out'
      }
    },
  },
  plugins: [
    require('tw-elements/dist/plugin')
  ],
}
