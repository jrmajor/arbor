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
		class={className}
		class:btn={!outline}
		class:btn-out={outline}
		class:small
	>
		{@render children()}
	</button>
{:else if !disabled}
	<a
		use:action={inertiaArgs ?? {}}
		{href}
		{rel}
		class={className}
		class:btn={!outline}
		class:disabled
		class:btn-out={outline}
		class:small
	>
		{@render children()}
	</a>
{:else}
	<span
		class={className}
		class:btn={!outline}
		class:disabled
		class:btn-out={outline}
		class:small
	>
		{@render children()}
	</span>
{/if}

<style lang="postcss">
  .btn {
    @apply inline-flex items-center;
    @apply rounded-md px-4 py-2 leading-5;
    @apply text-white bg-blue-600;
    @apply transition;

    &[disabled], &.disabled {
      @apply bg-gray-500;
      @apply cursor-default;
    }

    &:enabled {
      @apply hover:bg-blue-500;
      @apply focus:outline-none focus:border-blue-700 focus:ring;
      @apply active:bg-blue-700;
    }
  }

  .btn-out {
    @apply inline-flex items-center;
    @apply px-3 py-1 rounded-md;
    @apply border border-blue-700;
    @apply text-blue-700;
    @apply transition;

    &[disabled], &.disabled {
      @apply border-gray-500 text-gray-500;
      @apply cursor-default;
    }

    &:enabled {
      @apply hover:bg-blue-100 hover:text-blue-800;
      @apply focus:outline-none focus:ring;
      @apply active:bg-blue-600 active:border-blue-600 active:text-blue-100;
    }
  }

	.small {
		@apply leading-none text-xs rounded px-2;
	}
</style>
