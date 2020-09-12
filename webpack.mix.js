const mix = require('laravel-mix')
const tailwindcss = require('tailwindcss')

mix.js('resources/js/app.js', 'public/js')

mix.postCss('resources/css/app.css', 'public/css')
  .options({
    postCss: [ tailwindcss('tailwind.config.js') ],
  }).version()
