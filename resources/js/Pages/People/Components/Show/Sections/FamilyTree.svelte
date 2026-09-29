<script lang="ts">
	import type { Person, ShowPersonResource } from '@/types/resources/people';
	import toRoman from '@/helpers/toRoman';
	import Card from '../FamilyTree/Card.svelte';

	type Parent = NonNullable<ShowPersonResource['father']>;
	type Child = ShowPersonResource['children'][number];
	type Family = { key: string, partner: Person | null, label: string | null, children: Child[] };

	let { person }: { person: ShowPersonResource } = $props();

	let scroller = $state<HTMLElement>();

	let self = $derived<Person>({ ...person, visible: true });

	let hasParents = $derived(person.father !== null || person.mother !== null);

	let generation = $derived([
		...person.siblings.slice(0, person.siblingsBefore),
		null,
		...person.siblings.slice(person.siblingsBefore),
	]);

	let families = $derived.by(() => {
		const families: Family[] = person.marriages.map((marriage) => ({
			key: `marriage-${marriage.id}`,
			partner: marriage.partner,
			label: person.marriages.length > 1 && marriage.order ? `∞ ${toRoman(marriage.order)}` : '∞',
			children: [],
		}));

		let unknown: Family | null = null;

		for (const child of person.children) {
			const otherParentId = child.fatherId === person.id ? child.motherId : child.fatherId;
			const family = families.find((f) => f.partner?.id === otherParentId);

			if (family) {
				family.children.push(child);
			} else {
				unknown ??= { key: 'unknown', partner: null, label: null, children: [] };
				unknown.children.push(child);
			}
		}

		return unknown ? [...families, unknown] : families;
	});

	$effect(() => {
		void person.id;

		const current = scroller?.querySelector('[aria-current]');
		if (!scroller || !current) return;

		const s = scroller.getBoundingClientRect();
		const c = current.getBoundingClientRect();
		scroller.scrollLeft += (c.left + (c.width / 2)) - (s.left + (s.width / 2));
	});
</script>

{#if hasParents || families.length}
	<div class="rounded-lg bg-white shadow-sm">
		<div bind:this={scroller} class="overflow-x-auto p-6">
			<div class="tree mx-auto flex w-max flex-col items-center">
				{#if hasParents}
					<div class="up">
						{@render ancestor(person.father)}
						{@render ancestor(person.mother)}
					</div>
					<div class="stem"></div>
					<div class="down">
						{#each generation as sibling (sibling?.id ?? person.id)}
							<div class="node">
								{#if sibling}
									<div class="slot"><Card person={sibling}/></div>
								{:else}
									{@render descendants()}
								{/if}
							</div>
						{/each}
					</div>
				{:else}
					{@render descendants()}
				{/if}
			</div>
		</div>
	</div>
{/if}

{#snippet ancestor(parent: Parent | null)}
	<div class="node">
		{#if parent?.father || parent?.mother}
			<div class="up">
				<div class="node"><div class="slot"><Card person={parent.father}/></div></div>
				<div class="node"><div class="slot"><Card person={parent.mother}/></div></div>
			</div>
			<div class="stem"></div>
		{/if}
		<div class="slot"><Card person={parent}/></div>
	</div>
{/snippet}

{#snippet descendants()}
	<div class="slot"><Card person={self} current/></div>
	{#if families.length}
		<div class="stem"></div>
		<div class="down">
			{#each families as family (family.key)}
				<div class="node">
					<div class="slot"><Card person={family.partner} label={family.label}/></div>
					{#if family.children.length}
						<div class="stem"></div>
						<div class="down">
							{#each family.children as child (child.id)}
								<div class="node"><div class="slot"><Card person={child}/></div></div>
							{/each}
						</div>
					{/if}
				</div>
			{/each}
		</div>
	{/if}
{/snippet}

<style>
	/*
		Connectors are drawn with borders of pseudo-elements. Each node draws
		the horizontal bar on both sides of its center, as well as a vertical
		line from the bar to its card. The first and last node skip the outer
		halves, so the bar spans exactly between the outermost nodes, and round
		the corners where it turns towards their cards.

		Cards are centered in slots of the same height, so that rows are aligned.
		Slots draw the line through their center on connected sides, and cards
		cover the part of it that they overlap.
	*/

	.tree {
		--line: 1px;
		--gap: 1rem;
		--radius: 0.3125rem;
	}

	.up {
		display: grid;
		grid-template-columns: 1fr 1fr;
	}

	.down {
		display: flex;
		justify-content: center;
		align-items: flex-start;
	}

	.node {
		position: relative;
		display: flex;
		flex-direction: column;
		align-items: center;
		padding-inline: 0.375rem;
	}

	.up > .node {
		justify-content: flex-end;
		padding-bottom: var(--gap);
	}

	.down > .node {
		padding-top: var(--gap);
	}

	.node::before, .node::after {
		content: '';
		position: absolute;
		height: var(--gap);
		border-color: var(--color-gray-400);
	}

	/* left half of the bar */
	.node::before {
		left: 0;
		right: calc(50% - var(--line) / 2);
	}

	/* right half of the bar and the line to the card */
	.node::after {
		left: calc(50% - var(--line) / 2);
		right: 0;
		border-left-width: var(--line);
	}

	.up > .node::before, .up > .node::after {
		bottom: 0;
		border-bottom-width: var(--line);
	}

	.down > .node::before, .down > .node::after {
		top: 0;
		border-top-width: var(--line);
	}

	.node:first-child::before {
		display: none;
	}

	.up > .node:first-child::after {
		border-bottom-left-radius: var(--radius);
	}

	.down > .node:first-child::after {
		border-top-left-radius: var(--radius);
	}

	/* last node draws the line to the card with the left half, so that the corner can be rounded */
	.node:last-child::before {
		border-right-width: var(--line);
	}

	.node:last-child::after {
		display: none;
	}

	.up > .node:last-child::before {
		border-bottom-right-radius: var(--radius);
	}

	.down > .node:last-child::before {
		border-top-right-radius: var(--radius);
	}

	/* only node draws just the line to the card */
	.up > .node:only-child::after, .down > .node:only-child::after {
		display: block;
		border-top-width: 0;
		border-bottom-width: 0;
		border-radius: 0;
	}

	.slot {
		position: relative;
		isolation: isolate;
		display: flex;
		align-items: center;
		min-height: 5.25rem;
	}

	.slot::before, .slot::after {
		position: absolute;
		z-index: -1;
		left: calc(50% - var(--line) / 2);
		width: var(--line);
		background-color: var(--color-gray-400);
	}

	.down > .node > .slot:first-child::before, .stem + .slot::before {
		content: '';
		top: 0;
		bottom: 50%;
	}

	.up > .node > .slot:last-child::after, .slot:has(+ .stem)::after {
		content: '';
		top: 50%;
		bottom: 0;
	}

	.stem {
		width: var(--line);
		height: var(--gap);
		background-color: var(--color-gray-400);
	}
</style>
