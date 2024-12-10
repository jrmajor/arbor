import { render } from 'svelte/server';
import { createInertiaApp } from '@inertiajs/svelte';
import createServer from '@inertiajs/svelte/server';
import { resolve } from './common';

createServer((page) => createInertiaApp({
	page,
	resolve,
	setup: ({ App, props }) => {
		// @ts-expect-error
		globalThis.Ziggy = props.initialPage.props.ziggy;

		return render(App, { props });
	},
}));
