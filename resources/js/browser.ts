import { mount, hydrate } from 'svelte';
import { createInertiaApp } from '@inertiajs/svelte';
import { resolve } from './common';

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
