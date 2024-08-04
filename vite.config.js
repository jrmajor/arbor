import * as path from 'path'
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/style.css',
        'resources/js/classicApp.ts',
      ],
      refresh: true,
    }),
  ],
  resolve: {
    alias: {
      'ziggy-js': path.resolve(__dirname, 'vendor/tightenco/ziggy'),
    },
  },
})
