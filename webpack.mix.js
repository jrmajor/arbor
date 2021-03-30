const mix = require('laravel-mix')

mix
  .js('resources/js/app.js', 'public/js')
  .postCss('resources/css/app.css', 'public/css', [
    require('postcss-import'),
    require('@tailwindcss/jit'),
    require('postcss-nested'),
    require ('autoprefixer'),
  ])
  .version()

if (!mix.inProduction()) {
  require('laravel-mix-bundle-analyzer')

  mix.bundleAnalyzer({
    analyzerMode: 'static',
    openAnalyzer: false,
  })
}
