import { createInertiaApp } from '@inertiajs/svelte';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { mount, type Component } from 'svelte';
import AuthLayout from '@/Layouts/AuthLayout.svelte';
import Layout from '@/Layouts/Layout.svelte';

createInertiaApp({
	async resolve(name) {
		const page = await resolvePageComponent(
			`./Pages/${name}.svelte`,
			import.meta.glob<{ default: Component }>('./Pages/**/*.svelte'),
		);
		return {
			default: page.default,
			layout: name.startsWith('Auth/') ? AuthLayout : Layout,
		};
	},
	setup({ el, App, props }) {
		mount(App, { target: el, props });
	},
	progress: {
		color: '#4299e1',
	},
});
