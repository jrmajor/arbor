import type { ResolvedComponent } from '@inertiajs/svelte';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import AuthLayout from '@/Layouts/AuthLayout.svelte';
import Layout from '@/Layouts/Layout.svelte';
import TranslationsContext from '@/Layouts/TranslationContext.svelte';

export async function resolve(name: string): Promise<ResolvedComponent> {
	const page = await resolvePageComponent(
		`./Pages/${name}.svelte`,
		{
			...import.meta.glob<ResolvedComponent>('./Pages/*.svelte'),
			...import.meta.glob<ResolvedComponent>('./Pages/*/*.svelte'),
		},
	);
	return {
		default: page.default,
		layout: [
			// @ts-expect-error
			TranslationsContext,
			// @ts-expect-error
			name.startsWith('Auth/') ? AuthLayout : Layout,
			// @ts-expect-error
			...(page.layout ? [page.layout] : []),
		],
	};
}
