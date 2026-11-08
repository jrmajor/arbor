import { dirname } from 'path';
import inertia from '@inertiajs/vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { google } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';
import run from 'vite-plugin-run';
import fluent from './resources/js/viteFluent.ts';

const dir = import.meta.dirname;

export default defineConfig({
	plugins: [
		laravel({
			input: ['resources/js/app.ts', 'resources/css/style.css'],
			refresh: true,
			fonts: [
				google('Inter', {
					alias: 'sans',
					weights: ['100..900'],
					styles: ['normal', 'italic'],
					subsets: ['latin', 'latin-ext'],
					fallbacks: ['ui-sans-serif', 'system-ui', 'sans-serif'],
					optimizedFallbacks: false,
				}),
			],
		}),
		inertia({
			// one more than the default port
			ssr: { port: 13715 },
		}),
		run({
			name: 'ziggy',
			pattern: 'routes/*.php',
			run: ['php', 'artisan', 'ziggy:generate', '--types'],
		}),
		svelte({
			compilerOptions: {
				runes: ({ filename }) => filename.split('/').includes('node_modules') ? undefined : true,
			},
			inspector: true,
		}),
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
	server: {
		watch: {
			ignored: [
				`${dir}/storage/framework/views/**`,
				`${dir}/vendor/**`,
			],
		},
	},
});
