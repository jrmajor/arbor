<script lang="ts">
	import { type Snippet } from 'svelte';
	import type { HTMLAnchorAttributes } from 'svelte/elements';
	import { inertia } from '@inertiajs/svelte';
	import type { VisitOptions } from '@inertiajs/core';
	import { voidAction } from '@/helpers/utils';

	let {
		type = 'button',
		href = null,
		inertia: inertiaProp = null,
		onclick = () => null,
		rel = null,
		disabled = false,
		outline = false,
		small = false,
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
		children: Snippet;
	} = $props();

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
		class="btn"
		class:btn-solid={!outline}
		class:btn-outline={outline}
		class:small
	>
		{@render children()}
	</button>
{:else if !disabled}
	<a
		use:action={inertiaArgs ?? {}}
		{href}
		{rel}
		class="btn"
		class:disabled
		class:btn-solid={!outline}
		class:btn-outline={outline}
		class:small
	>
		{@render children()}
	</a>
{:else}
	<span
		class="btn"
		class:disabled
		class:btn-solid={!outline}
		class:btn-outline={outline}
		class:small
	>
		{@render children()}
	</span>
{/if}

<style lang="postcss">
	.btn {
		display: inline-flex;
		align-items: center;
		@apply rounded-md;
		@apply transition;

		&[disabled], &.disabled {
			cursor: default;
		}
	}

	.btn-solid {
		@apply px-4 py-1.5;
		@apply text-white bg-blue-600;

		&[disabled], &.disabled {
			@apply bg-gray-500;
		}

		&:not([disabled]):not(.disabled) {
			@apply hover:bg-blue-500;
			@apply focus:outline-none focus:border-blue-700 focus:ring;
			@apply active:bg-blue-700;
		}
	}

	.btn-outline {
		@apply px-3 py-1;
		@apply border border-blue-700;
		@apply text-blue-700;

		&[disabled], &.disabled {
			@apply border-gray-500 text-gray-500;
		}

		&:not([disabled]):not(.disabled) {
			@apply hover:bg-blue-300/20 hover:text-blue-800;
			@apply focus:outline-none focus:ring;
			@apply active:bg-blue-600 active:border-blue-600 active:text-blue-100;
		}
	}

	.small {
		@apply leading-none text-xs rounded px-2;
	}
</style>
