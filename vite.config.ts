import { dirname } from 'path';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import run from 'vite-plugin-run';
import inertia from './resources/js/inertiaVitePlugin';
import fluent from './resources/js/viteFluent';

export default defineConfig({
	plugins: [
		laravel({
			input: ['resources/js/browser.ts', 'resources/css/style.css'],
			ssr: 'resources/js/ssr.ts',
			refresh: true,
		}),
		inertia('resources/js/viteSsr.ts'),
		run({
			name: 'ziggy',
			pattern: 'routes/*.php',
			run: ['php', 'artisan', 'ziggy:generate', '--types'],
			build: false,
		}),
		svelte(),
		tailwindcss(),
		fluent({
			resolveLocale(path) {
				return dirname(path).slice(-2);
			},
		}),
	],
	build: {
		assetsInlineLimit: 4096,
	},
	resolve: {
		tsconfigPaths: true,
		alias: {
			$style: `${import.meta.dirname}/resources/css/style.css`,
		},
	},
});
