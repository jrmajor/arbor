import { dirname, resolve } from 'path';
import { svelte, vitePreprocess } from '@sveltejs/vite-plugin-svelte';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import fluent from './resources/js/viteFluent';

export default defineConfig({
	plugins: [
		laravel({
			input: ['resources/js/browser.ts', 'resources/css/style.css'],
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
		fluent({
			resolveLocale(path) {
				return dirname(path).slice(-2);
			},
		}),
	],
	resolve: {
		alias: {
			'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
		},
	},
});
