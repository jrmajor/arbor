const mix = require('laravel-mix')
const tailwindcss = require('tailwindcss')
const path = require('path')

mix.js('resources/js/app.js', 'public/js')

mix.postCss('resources/css/app.css', 'public/css')
  .options({
    postCss: [tailwindcss('tailwind.config.js')]
  }).version()

if (!mix.inProduction()) {
  require('laravel-mix-bundle-analyzer')

  mix.bundleAnalyzer({
    analyzerMode: 'static',
    openAnalyzer: false
  })
}

mix.webpackConfig({
  resolve: {
    alias: {
      ziggy: path.resolve('vendor/tightenco/ziggy/src/js/route.js')
    }
  }
})
