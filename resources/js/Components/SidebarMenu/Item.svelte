<script lang="ts">
	import { type Snippet } from 'svelte';
	import { inertia } from '@inertiajs/svelte';
	import { hotkey as hotkeyAction } from '@/helpers/hotkey';
	import { t } from '@/helpers/translations';
	import { voidAction } from '@/helpers/utils';

	let {
		name,
		href,
		hotkey = null,
		visitOptions = {},
		active = false,
		danger = false,
		children,
	}: {
		name: string;
		href: string;
		hotkey?: string | null;
		visitOptions?: (Omit<VisitOptions, 'href' | 'onBefore'> & { confirm?: string });
		active?: boolean;
		danger?: boolean;
		children: Snippet;
	} = $props();

	type VisitOptions = NonNullable<Parameters<typeof inertia>[1]>;

	let shouldBeLink = $derived((visitOptions.method ?? 'get') === 'get');

	let optionalHotkeyAction = $derived(hotkey ? hotkeyAction : voidAction);

	function onBefore() {
		if (!visitOptions.confirm) return true;
		return confirm(visitOptions.confirm);
	}
</script>

{#if active}
	<span class="text-blue-700 transition">
		<li class="px-3 py-1 rounded-sm transition">
			<span class="flex w-full items-center border-b-2 border-dotted border-blue-500 uppercase">
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
	<svelte:element
		this={shouldBeLink ? 'a' : 'button'}
		use:inertia={{ ...visitOptions, href, onBefore }}
		use:optionalHotkeyAction={hotkey}
		href={shouldBeLink ? href : null}
		class="
			group block w-full uppercase transition focus:outline-none
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
	</svelte:element>
{/if}
