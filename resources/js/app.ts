import type { Component } from 'svelte';
import { createInertiaApp } from '@inertiajs/svelte';
import type { ResolvedComponent } from '@inertiajs/svelte';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import AuthLayout from '@/Layouts/AuthLayout.svelte';
import Layout from '@/Layouts/Layout.svelte';
import TranslationsContext from '@/Layouts/TranslationContext.svelte';
import { Ziggy } from './ziggy/index.js';

globalThis.Ziggy = Ziggy;

createInertiaApp({
	async resolve(name: string) {
		const page = await resolvePageComponent(
			`./Pages/${name}.svelte`,
			{
				...import.meta.glob<ResolvedComponent>('./Pages/*.svelte'),
				...import.meta.glob<ResolvedComponent>('./Pages/*/*.svelte'),
			},
		);
		return {
			...page,
			layout: [
				TranslationsContext as Component,
				name.startsWith('Auth/') ? AuthLayout : Layout,
				...(page.layout ? [page.layout] : []),
			] as Component[],
		};
	},
	progress: {
		color: '#4299e1',
	},
});
