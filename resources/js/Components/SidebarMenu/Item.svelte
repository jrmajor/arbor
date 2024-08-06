<script lang="ts">
	import { type Snippet } from 'svelte';
	import { inertia } from '@inertiajs/svelte';
	import { type VisitOptions } from '@inertiajs/core';
	import { t } from '@/helpers/translations';

	let {
		name,
		href,
		visitOptions = null,
		active = false,
		danger = false,
		children,
	}: {
		name: string;
		href: string;
		visitOptions?: (Omit<VisitOptions, 'onBefore'> & { confirm?: string }) | null;
		active?: boolean;
		danger?: boolean;
		children: Snippet;
	} = $props();

	let computedVisitOptions: VisitOptions = $derived.by(() => {
		if (!visitOptions) {
			return {
				onBefore() {
					window.location.href = href;
					return false;
				},
			};
		}
		if (!visitOptions.confirm) return visitOptions;
		return {
			...visitOptions,
			onBefore: () => confirm(visitOptions.confirm),
		};
	});
</script>

{#if active}
	<span class="text-blue-700 transition">
		<li class="px-3 py-1 rounded transition">
			<span class="w-full border-b-2 border-dotted border-blue-500 flex items-center">
				<svg
					viewBox="0 0 20 20"
					xmlns="http://www.w3.org/2000/svg"
					class="size-4 mr-2 fill-current text-blue-600 transition"
				>
					{@render children()}
				</svg>
				{t(name)}
			</span>
		</li>
	</span>
{:else}
	<a
		use:inertia={computedVisitOptions}
		href={href}
		class="
			group focus:outline-none transition
			{danger ? 'text-red-600 hover:text-red-700 focus:text-red-700' : 'text-gray-700 hover:text-gray-800 focus:text-gray-800'}
		"
	>
		<li
			class="px-3 py-1 rounded transition
				{danger ? 'group-hover:bg-red-200 group-focus:bg-red-300' : 'group-hover:bg-gray-200 group-focus:bg-gray-300'}"
		>
			<span class="w-full flex items-center">
				<svg
					class="size-4 mr-2 fill-current transition
						{danger ? '' : 'text-gray-600 group-hover:text-gray-700 group-focus:text-gray-700'}"
					viewBox="0 0 20 20"
					xmlns="http://www.w3.org/2000/svg"
				>
					{@render children()}
				</svg>
				{t(name)}
			</span>
		</li>
	</a>
{/if}
