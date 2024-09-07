import { type Component } from 'svelte';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import AuthLayout from '@/Layouts/AuthLayout.svelte';
import Layout from '@/Layouts/Layout.svelte';
import TranslationsContext from '@/Layouts/TranslationContext.svelte';

export async function resolve(name: string) {
	const page = await resolvePageComponent(
		`./Pages/${name}.svelte`,
		import.meta.glob<ComponentFile>('./Pages/**/*.svelte'),
	);
	return {
		default: page.default,
		layout: [
			TranslationsContext,
			name.startsWith('Auth/') ? AuthLayout : Layout,
			...(page.layout ? [page.layout] : []),
		],
	};
}

type ComponentFile = {
	default: Component;
	layout?: Component;
};
