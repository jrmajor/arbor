<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { HTMLAnchorAttributes, HTMLButtonAttributes } from 'svelte/elements';
	import { inertia } from '@/helpers/inertia';

	type Props = {
		isActive?: boolean;
		mobile?: boolean;
		children: Snippet;
	} & (
		({ this?: 'a' } & Pick<HTMLAnchorAttributes, 'href' | 'class' | symbol>)
		| ({ this: 'button' } & Pick<HTMLButtonAttributes, 'onclick' | 'class' | symbol>)
	);

	let {
		this: as = 'a',
		isActive = false,
		mobile = false,
		class: className,
		children,
		...props
	}: Props = $props();
</script>

<svelte:element
	this={as}
	{@attach as === 'a' && inertia()}
	{...props}
	class={[
		'flex items-center gap-2 rounded-md',
		mobile
			? 'px-2.5 py-1'
			: 'px-2 py-1.5',
		'transition-colors duration-200',
		'focus:outline-none hover:no-underline',
		isActive
			? 'bg-blue-500/10 text-blue-700'
			: [
				'text-gray-800',
				' hover:text-gray-900 hover:bg-gray-100',
				' focus-visible:text-gray-900 focus-visible:bg-gray-100',
				'active:bg-blue-500/10 active:text-blue-700',
			],
		className,
	]}
>
	{@render children()}
</svelte:element>
