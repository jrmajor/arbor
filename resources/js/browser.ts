import { hydrate, mount } from 'svelte';
import { createInertiaApp } from '@inertiajs/svelte';
import { Ziggy } from '@/ziggy/index.js';
import { resolve } from './common';

globalThis.Ziggy = Ziggy;

createInertiaApp({
	resolve,
	setup({ el, App, props }) {
		if ((el as HTMLElement).dataset.serverRendered) {
			// @ts-expect-error
			hydrate(App, { target: el, props });
		} else {
			// @ts-expect-error
			mount(App, { target: el, props });
		}
	},
	progress: {
		color: '#4299e1',
	},
});
