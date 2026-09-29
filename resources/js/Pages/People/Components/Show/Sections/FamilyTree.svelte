<script lang="ts">
	import type { ShowPersonResource } from '@/types/resources/people';
	import Card from '../FamilyTree/Card.svelte';
	import { cardWidth, layoutTree, slotHeight } from '../FamilyTree/layout';

	let { person }: { person: ShowPersonResource } = $props();

	let scroller = $state<HTMLElement>();
	let available = $state(0);

	let tree = $derived(layoutTree(person));

	let center = $derived(tree.cards.find((card) => card.current)!.left + (cardWidth / 2));

	// leave enough scroll space to center the person near either edge
	let padding = $derived(tree.width > available
		? [Math.max(0, (available / 2) - center), Math.max(0, (available / 2) - (tree.width - center))]
		: [0, 0]);

	$effect(() => {
		void padding;

		const current = scroller?.querySelector('[aria-current]');
		if (!scroller || !current) return;

		const s = scroller.getBoundingClientRect();
		const c = current.getBoundingClientRect();
		scroller.scrollLeft += (c.left + (c.width / 2)) - (s.left + (s.width / 2));
	});
</script>

{#if tree.cards.length > 1}
	<div class="rounded-lg bg-white shadow-sm">
		<div bind:this={scroller} class="overflow-x-auto p-6">
			<div bind:clientWidth={available}>
				<div class="mx-auto w-max" style:padding-left="{padding[0]}px" style:padding-right="{padding[1]}px">
					<div class="relative" style:width="{tree.width}px" style:height="{tree.height}px">
						<svg class="absolute inset-0 overflow-visible text-gray-400" width={tree.width} height={tree.height} aria-hidden="true">
							<!-- half a pixel moves 1px lines to pixel grid -->
							<g transform="translate({tree.origin[0] + 0.5} {tree.origin[1] + 0.5})" fill="none" stroke="currentColor">
								{#each tree.paths as d}
									<path {d}/>
								{/each}
							</g>
						</svg>

						{#each tree.cards as card (card.key)}
							<div
								class="absolute flex items-center justify-center"
								style:left="{card.left}px"
								style:top="{card.top}px"
								style:width="{cardWidth}px"
								style:height="{slotHeight}px"
							>
								<Card person={card.person} label={card.label} current={card.current}/>
							</div>
						{/each}
					</div>
				</div>
			</div>
		</div>
	</div>
{/if}
