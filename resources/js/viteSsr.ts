import { render } from 'svelte/server';
import type { InertiaAppResponse, Page } from '@inertiajs/core';
import { createInertiaApp } from '@inertiajs/svelte';
import { resolve } from './common';

export default function viteSsr(page: Page): InertiaAppResponse {
	return createInertiaApp({
		page,
		resolve,
		setup: ({ App, props }) => {
			// @ts-expect-error
			globalThis.Ziggy = props.initialPage.props.ziggy;

			return render(App, { props });
		},
	});
}
