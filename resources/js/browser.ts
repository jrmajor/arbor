import { mount } from 'svelte';
import { createInertiaApp } from '@inertiajs/svelte';
import { resolve } from './common';

createInertiaApp({
	resolve,
	setup({ el, App, props }) {
		// @ts-expect-error
		mount(App, { target: el, props });
	},
	progress: {
		color: '#4299e1',
	},
});
