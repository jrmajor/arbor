<script lang="ts">
	import { route } from 'ziggy-js';
	import type { ShowPersonResource } from '@/types/resources/people';
	import { inertia } from '@/helpers/inertia';
	import { t } from '@/helpers/translations';
	import { ageAt, buildTimeline, formatYears, type Marker, type Row } from '../Timeline/rows';

	let { person }: { person: ShowPersonResource } = $props();

	// keep year labels at least this far apart
	const minTickSpacing = 48;

	// merge markers closer than this into one badge, so they don't overlap
	const minMarkerSpacing = 24;

	let timeline = $derived(buildTimeline(person, new Date()));

	let rowsByKey = $derived(new Map(timeline.groups.flatMap((group) => group.rows).map((row) => [row.key, row])));

	let track = $state<HTMLElement>();
	let width = $state(0);

	let step = $derived.by(() => {
		const span = timeline.max - timeline.min;
		const available = width || 600;
		return [5, 10, 20, 25, 50, 100, 200].find((s) => (span / s) * minTickSpacing <= available) ?? 500;
	});

	let min = $derived(Math.floor(timeline.min / step) * step);
	// end at the current date when anyone is alive, so their bars reach the edge
	let max = $derived(timeline.now ?? Math.ceil(timeline.max / step) * step);

	let ticks = $derived(Array.from({ length: Math.floor((max - min) / step) + 1 }, (_, i) => min + (i * step)));

	let cursor = $state<number | null>(null);

	// the hovered row and badge, and the pointer position for the hover card
	let chart = $state<HTMLElement>();
	let hoveredRow = $state<Row | null>(null);
	let hoveredBadge = $state<string | null>(null);
	let pointer = $state({ x: 0, y: 0 });
	let chartWidth = $state(0);
	let cardWidth = $state(0);

	// the hover card shows the events of the hovered marriage badge
	let card = $derived(hoveredRow && hoveredBadge
		? badges(hoveredRow.markers).find((badge) => badge.key === hoveredBadge) ?? null
		: null);

	// ages sit right of the cursor, except near the right edge
	let agesOnLeft = $derived(cursor !== null && cursor > max - ((max - min) * 0.15));

	// put the hover card opposite the ages, or past them when it doesn't fit there
	let cardTransform = $derived.by(() => {
		const gap = 12;
		const pastAges = 52;

		let onLeft = !agesOnLeft;
		if (onLeft && pointer.x - gap - cardWidth < 0) onLeft = false;
		if (!onLeft && pointer.x + gap + cardWidth > chartWidth) onLeft = true;

		const offset = onLeft === agesOnLeft ? pastAges : gap;
		return onLeft ? `translate(calc(-100% - ${offset}px), -50%)` : `translate(${offset}px, -50%)`;
	});

	function x(year: number) {
		return ((year - min) / (max - min)) * 100;
	}

	function point(event: PointerEvent) {
		if (!track || !chart) return;

		const rect = track.getBoundingClientRect();
		const fraction = (event.clientX - rect.left) / rect.width;
		cursor = fraction < 0 || fraction > 1 ? null : min + (fraction * (max - min));

		const outer = chart.getBoundingClientRect();
		pointer = { x: event.clientX - outer.left, y: event.clientY - outer.top };

		// hit-test instead of using enter and leave events, which a touch drag
		// doesn't fire because the element it started on captures the pointer
		const target = document.elementFromPoint(event.clientX, event.clientY);
		const rowKey = target?.closest<HTMLElement>('[data-row]')?.dataset.row;
		hoveredRow = rowKey ? rowsByKey.get(rowKey) ?? null : null;
		hoveredBadge = target?.closest<HTMLElement>('[data-badge]')?.dataset.badge ?? null;
	}

	function leave(event: PointerEvent) {
		// keep the cursor where a touch left it
		if (event.pointerType !== 'mouse') return;

		cursor = null;
		hoveredRow = null;
		hoveredBadge = null;
	}

	function gradient(row: Row) {
		const length = row.end.to - row.start.from;
		// born and died in the same year
		if (length <= 0) return 'currentColor';

		const solidFrom = ((row.start.to - row.start.from) / length) * 100;
		const solidTo = ((row.end.from - row.start.from) / length) * 100;

		return `linear-gradient(to right, transparent, currentColor ${solidFrom}%, currentColor ${solidTo}%, transparent)`;
	}

	// group markers into badges, chaining each one to the previous one when they're too close
	function badges(markers: Marker[]) {
		const groups: Marker[][] = [];

		for (const marker of markers.toSorted((a, b) => a.at - b.at)) {
			const last = groups.at(-1);
			if (last && toPixels(marker.at - last.at(-1)!.at) < minMarkerSpacing) {
				last.push(marker);
			} else {
				groups.push([marker]);
			}
		}

		return groups.map((group) => ({
			key: group.map((m) => m.key).join(),
			at: (group[0].at + group.at(-1)!.at) / 2,
			markers: group,
		}));
	}

	function toPixels(years: number) {
		return (years / (max - min)) * (width || 600);
	}

	function markerTitle(marker: Marker) {
		const type = marker.type === 'divorce' ? t('marriages.divorced').toLowerCase() : t('marriages.marriage');
		return `${type} ${formatYears(marker.years)}`;
	}

</script>

{#if rowsByKey.size > 1}
	<div class="rounded-lg bg-white p-6 shadow-sm">
		<div
			bind:this={chart}
			bind:clientWidth={chartWidth}
			class="relative touch-pan-y select-none [--label:0px] sm:[--label:12rem]"
			onpointermove={point}
			onpointerdown={point}
			onpointerleave={leave}
			role="presentation"
		>
			<div class="grid grid-cols-[var(--label)_1fr]">
				<div></div>
				<div bind:this={track} bind:clientWidth={width} class="relative h-5 text-xs text-gray-500 tabular-nums">
					{#each ticks as tick (tick)}
						<span class="absolute -translate-x-1/2" style:left="{x(tick)}%">{tick}</span>
					{/each}
					{#if cursor !== null}
						<span class="absolute z-1 -translate-x-1/2 rounded-sm bg-white px-1 font-medium text-blue-700" style:left="{x(cursor)}%">
							{Math.floor(cursor)}
						</span>
					{/if}
				</div>
			</div>

			<div class="pointer-events-none absolute top-6 right-0 bottom-0 left-(--label)" aria-hidden="true">
				{#each ticks as tick, i (tick)}
					<!-- on phones the first line would run along the names -->
					<div class={['absolute inset-y-0 border-l border-gray-100', i === 0 && 'max-sm:hidden']} style:left="{x(tick)}%"></div>
				{/each}
				{#if timeline.now !== null}
					<div class="absolute inset-y-0 right-0 border-r border-dashed border-gray-300"></div>
				{/if}
				{#if cursor !== null}
					<div class="absolute inset-y-0 z-1 border-l border-blue-400" style:left="{x(cursor)}%"></div>
				{/if}
			</div>

			{#each timeline.groups as group (group.key)}
				{#if group.label}
					<div class="relative mt-3 text-xs font-medium text-gray-400"><span class="backdrop">{t(group.label)}</span></div>
				{/if}

				{#each group.rows as row (row.key)}
					{@const age = cursor === null ? null : ageAt(row, cursor)}
					<div class="relative grid grid-cols-1 items-center py-0.5 sm:grid-cols-[var(--label)_1fr]" data-row={row.key}>
						<!-- on phones names sit above the bars, so their background cuts the gridlines behind them -->
						<div class="pr-3 text-sm leading-5" class:italic={row.person.isDead}>
							<span class="backdrop">
								<span class="block truncate">
									{#if row.current}
										<span class="font-medium" aria-current="page">{@render name()}</span>
									{:else}
										<a {@attach inertia()} href={route('people.show', row.person)} class="text-blue-700 hover:underline">
											{@render name()}
										</a>
									{/if}
								</span>
							</span>
						</div>

						<div class="relative h-5">
							<!-- unknown deaths may fade out past the current date -->
							<div class="absolute inset-0 overflow-hidden">
								<div
									class={[
										'absolute top-1/2 h-2.5 min-w-1.5 -translate-y-1/2',
										row.current ? 'text-blue-600' : 'text-gray-400',
										row.start.from === row.start.to && 'rounded-l-full',
										row.end.from === row.end.to && !row.alive && 'rounded-r-full',
									]}
									style:left="min({x(row.start.from)}%, 100% - 0.375rem)"
									style:width="{x(row.end.to) - x(row.start.from)}%"
									style:background={gradient(row)}
								></div>
							</div>

							{#each badges(row.markers) as badge (badge.key)}
								<span
									class={[
										'absolute top-1/2 z-2 flex -translate-1/2 gap-1 rounded-sm bg-white px-1 text-xs leading-4 text-gray-700 ring-1',
										hoveredBadge === badge.key ? 'ring-blue-400' : 'ring-gray-300',
									]}
									style:left="{x(badge.at)}%"
									data-badge={badge.key}
									aria-label={badge.markers.map(markerTitle).join(', ')}
									role="img"
								>
									{#each badge.markers as marker (marker.key)}
										<span class={['relative', marker.type === 'divorce' && 'divorce']}>∞</span>
									{/each}
								</span>
							{/each}

							{#if age !== null}
								<span
									class={[
										'absolute top-1/2 z-3 -translate-y-1/2 rounded-sm bg-white/85 px-1 text-xs font-medium text-blue-700 tabular-nums',
										agesOnLeft ? '-translate-x-full -ml-1' : 'ml-1',
									]}
									style:left="{x(cursor!)}%"
								>{age}</span>
							{/if}
						</div>
					</div>

					{#snippet name()}
						{row.person.name}
						<span class="font-medium">{row.person.lastName ?? row.person.familyName}</span>
						{#if row.person.lastName}
							<span class="text-xs">({row.person.familyName})</span>
						{/if}
					{/snippet}
				{/each}
			{/each}

			{#if card}
				<div
					class="pointer-events-none absolute z-10 w-max max-w-64 rounded-md bg-white px-2.5 py-1.5 text-xs leading-5 text-gray-700 shadow-md ring-1 ring-gray-200"
					style:left="{pointer.x}px"
					style:top="{pointer.y}px"
					style:transform={cardTransform}
					bind:offsetWidth={cardWidth}
					aria-hidden="true"
				>
					{#each card.markers as marker (marker.key)}
						<p>{markerTitle(marker)}</p>
					{/each}
				</div>
			{/if}
		</div>
	</div>
{/if}

<style>
	/* a white backdrop that fades out a little beyond the text */
	.backdrop {
		position: relative;
		isolation: isolate;
		display: inline-block;
		max-width: 100%;
		vertical-align: top;

		&::before {
			content: '';
			position: absolute;
			inset: -6px -10px;
			z-index: -1;
			background: white;
			mask-image:
				linear-gradient(to right, transparent, black 10px, black calc(100% - 10px), transparent),
				linear-gradient(to bottom, transparent, black 6px, black calc(100% - 6px), transparent);
			mask-composite: intersect;
		}
	}

	/* a diagonal stroke across the ∞ */
	.divorce::after {
		content: '';
		position: absolute;
		inset: 1px -1px;
		background: linear-gradient(
			to top right,
			transparent calc(50% - 0.6px),
			currentColor calc(50% - 0.6px) calc(50% + 0.6px),
			transparent calc(50% + 0.6px)
		);
	}
</style>
