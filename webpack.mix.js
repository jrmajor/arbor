const mix = require('laravel-mix')

mix
  .js('resources/js/app.js', 'public/js')
  .postCss('resources/css/style.css', 'public/css', [
    require('postcss-import'),
    require('tailwindcss/nesting'),
    require('tailwindcss'),
    require ('autoprefixer'),
  ])
  .version()
