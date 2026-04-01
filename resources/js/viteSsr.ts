import { render } from 'svelte/server';
import type { InertiaAppResponse, Page } from '@inertiajs/core';
import { createInertiaApp } from '@inertiajs/svelte';
import { Ziggy } from '@/ziggy/index.js';
import { resolve } from './common';

globalThis.Ziggy = Ziggy;

export default function viteSsr(page: Page): InertiaAppResponse {
	return createInertiaApp({
		page,
		resolve,
		setup: ({ App, props }) => render(App, { props }),
	});
}
