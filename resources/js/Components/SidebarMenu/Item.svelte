<script lang="ts">
	import { hotkey as hotkeyAttachment } from '@/helpers/hotkey';
	import { inertia, type VisitOptions } from '@/helpers/inertia';
	import { t } from '@/helpers/translations';
	import Icon, { type IconData } from '@/Components/Icons/Icon.svelte';

	let {
		name,
		href,
		hotkey = null,
		visitOptions = { },
		icon,
		active = false,
		danger = false,
	}: {
		name: string;
		href: string;
		hotkey?: string | null;
		visitOptions?: (Omit<VisitOptions, 'href' | 'onBefore'> & { confirm?: string });
		icon: IconData;
		active?: boolean;
		danger?: boolean;
	} = $props();

	let shouldBeLink = $derived((visitOptions.method ?? 'get') === 'get');

	function onBefore() {
		if (!visitOptions.confirm) return true;
		return confirm(visitOptions.confirm);
	}
</script>

{#if active}
	<span class="text-blue-700 transition">
		<li class="px-3 py-1 rounded-sm transition">
			<span class="flex w-full items-center border-b-2 border-dotted border-blue-500 uppercase">
				<Icon {icon} class="size-4 mr-2 text-blue-600 transition"/>
				{t(name)}
			</span>
		</li>
	</span>
{:else}
	<svelte:element
		this={shouldBeLink ? 'a' : 'button'}
		{@attach inertia({ ...visitOptions, href, onBefore })}
		{@attach hotkey ? hotkeyAttachment(hotkey) : null}
		href={shouldBeLink ? href : null}
		class={[
			'group block w-full uppercase transition focus:outline-none',
			danger ? 'text-red-600 hover:text-red-700 focus:text-red-700' : 'text-gray-700 hover:text-gray-800 focus:text-gray-800',
		]}
	>
		<li
			class={[
				'px-3 py-1 rounded transition',
				danger ? 'group-hover:bg-red-200 group-focus:bg-red-300' : 'group-hover:bg-gray-200 group-focus:bg-gray-300',
			]}
		>
			<span class="w-full flex items-center">
				<Icon
					{icon}
					class={[
						'size-4 mr-2 transition',
						danger || 'text-gray-600 group-hover:text-gray-700 group-focus:text-gray-700',
					]}
				/>
				{t(name)}
			</span>
		</li>
	</svelte:element>
{/if}
