const mix = require('laravel-mix')
const tailwindcss = require('tailwindcss')
require('laravel-mix-purgecss')

mix.js('resources/js/app.js', 'public/js')
    .options({
      terser: { extractComments: false }
    })

mix.sass('resources/sass/app.scss', 'public/css')
    .options({
        processCssUrls: false,
        postCss: [ tailwindcss('tailwind.config.js') ],
    }).purgeCss({
        extractorPattern: /[\w-/:]+(?<!:)/g
    }).version()
