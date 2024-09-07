import * as path from 'path';
import { svelte, vitePreprocess } from '@sveltejs/vite-plugin-svelte';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

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
	],
	resolve: {
		alias: {
			'ziggy-js': path.resolve(__dirname, 'vendor/tightenco/ziggy'),
		},
	},
});
