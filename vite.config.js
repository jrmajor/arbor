import * as path from 'path';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { svelte, vitePreprocess } from '@sveltejs/vite-plugin-svelte';

export default defineConfig({
	plugins: [
		laravel({
			input: [
				'resources/css/style.css',
				'resources/js/classicApp.ts',
				'resources/js/browser.ts',
			],
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
