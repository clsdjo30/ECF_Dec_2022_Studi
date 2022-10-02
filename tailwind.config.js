/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig',
    './node_modules/tw-elements/dist/js/**/*.js'
  ],
  theme: {
    fontFamily: {
      'menu-item': ['Raleway'],
      'title': ['Rokkitt'],
    },
    extend: {},
  },
  plugins: [
    require('tw-elements/dist/plugin')
  ],
}
