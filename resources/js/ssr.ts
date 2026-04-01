import { render } from 'svelte/server';
import { createInertiaApp } from '@inertiajs/svelte';
import createServer from '@inertiajs/svelte/server';
import { Ziggy } from '@/ziggy/index.js';
import { resolve } from './common';

globalThis.Ziggy = Ziggy;

createServer((page) => createInertiaApp({
	page,
	resolve,
	setup: ({ App, props }) => render(App, { props }),
}), 13715);
