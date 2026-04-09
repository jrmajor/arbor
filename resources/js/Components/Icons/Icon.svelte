<script module lang="ts">
	import type { SVGAttributes } from 'svelte/elements';

	export type IconData = {
		viewBox?: number;
		solid?: true;
		nodes: Array<{ node: 'path' } & SVGAttributes<SVGPathElement>>;
	} | string;

	export * as icons from './icons';
</script>

<script lang="ts">
	import type { ClassValue } from 'svelte/elements';

	let { icon, class: className }: { icon: IconData, class?: ClassValue } = $props();

	const {
		viewBox = 20,
		solid = false,
		nodes,
	} = $derived(
		typeof icon === 'string'
			? ({ nodes: [{ node: 'path', d: icon }] } satisfies IconData)
			: icon,
	);
</script>

<svg
	xmlns="http://www.w3.org/2000/svg"
	viewBox="0 0 {viewBox} {viewBox}"
	fill={solid ? 'none' : 'currentColor'}
	stroke={solid ? 'currentColor' : 'none'}
	width={20}
	height={20}
	class={className}
>
	{#each nodes as { node, ...attrs }}
		<svelte:element this={node} {...attrs}/>
	{/each}
</svg>
