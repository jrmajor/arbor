import { dirname } from 'path';
import { svelte, vitePreprocess } from '@sveltejs/vite-plugin-svelte';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import inertia from './resources/js/inertiaVitePlugin';
import fluent from './resources/js/viteFluent';

export default defineConfig({
	plugins: [
		laravel({
			input: ['resources/js/browser.ts', 'resources/css/style.css'],
			ssr: 'resources/js/ssr.ts',
			refresh: true,
		}),
		svelte({
			preprocess: [vitePreprocess()],
			dynamicCompileOptions({ filename }) {
				if (!filename.includes('node_modules')) {
					return { runes: true };
				}
			},
		}),
		tailwindcss(),
		fluent({
			resolveLocale(path) {
				return dirname(path).slice(-2);
			},
		}),
		inertia('resources/js/viteSsr.ts'),
	],
	build: {
		assetsInlineLimit: 4096,
	},
	resolve: {
		alias: {
			'ziggy-js': `${import.meta.dirname}/vendor/tightenco/ziggy`,
			$style: `${import.meta.dirname}/resources/css/style.css`,
		},
	},
});
