<script lang="ts">
	import { type Snippet } from 'svelte';
	import type { Action } from 'svelte/action';
	import type { HTMLAnchorAttributes } from 'svelte/elements';
	import { inertia } from '@inertiajs/svelte';
	import type { VisitOptions } from '@inertiajs/core';

	let {
		type = 'button',
		href = null,
		inertia: inertiaProp = null,
		onclick = () => null,
		rel = null,
		disabled = false,
		outline = false,
		small = false,
		red = false,
		class: className = '',
		children,
	}: {
		type?: 'button' | 'submit' | 'link';
		href?: string | null;
		inertia?: (VisitOptions & { href?: string }) | null;
		onclick?: (event: MouseEvent) => void;
		rel?: HTMLAnchorAttributes['rel'];
		disabled?: boolean;
		outline?: boolean;
		small?: boolean;
		red?: boolean;
		class?: string;
		children: Snippet;
	} = $props();

	// eslint-disable-next-line func-style
	const voidAction: Action<HTMLButtonElement, any> = () => ({});

	let inertiaArgs = $derived.by(() => {
		if (type === 'link') return inertiaProp ?? {};
		if (href) return { ...(inertiaProp ?? {}), href };
		return inertiaProp;
	});
	let action = $derived(inertiaArgs ? inertia : voidAction);
</script>

{#if type !== 'link'}
	<button
		use:action={inertiaArgs ?? {}}
		{onclick}
		{type}
		{disabled}
		class="{className} {small ? 'leading-none text-xs rounded px-2' : ''}"
		class:btn={!outline}
		class:btn-red={red}
		class:btn-out={outline}
	>
		{@render children()}
	</button>
{:else if !disabled}
	<a
		use:action={inertiaArgs ?? {}}
		{href}
		{rel}
		class="{className} {small ? 'leading-none text-xs rounded px-2' : ''}"
		class:btn={!outline}
		class:btn-red={red}
		class:btn-out={outline}
		class:disabled
	>
		{@render children()}
	</a>
{:else}
	<span
		class="{className} {small ? 'leading-none text-xs rounded px-2' : ''}"
		class:btn={!outline}
		class:btn-red={red}
		class:btn-out={outline}
		class:disabled
	>
		{@render children()}
	</span>
{/if}
