import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel([
      'resources/css/style.css',
      'resources/js/app.ts',
    ]),
    {
      name: 'blade',
      handleHotUpdate({ file, server }) {
        if (! file.endsWith('.blade.php')) {
          return
        }

        server.ws.send({ type: 'full-reload', path: '*' })
      },
    },
  ],
})
