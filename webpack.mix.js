const mix = require('laravel-mix')

mix
  .ts('resources/js/app.ts', 'public/js')
  .postCss('resources/css/style.css', 'public/css', [
    require('postcss-import'),
    require('tailwindcss/nesting'),
    require('tailwindcss'),
    require ('autoprefixer'),
  ])
  .version()
