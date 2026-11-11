<script lang="ts">
	import { route } from 'ziggy-js';
	import type { ShowPersonResource } from '@/types/resources/people';
	import { inertia } from '@/helpers/inertia';
	import { t } from '@/helpers/translations';
	import { ageAt, buildTimeline, formatYears, type Row } from '../Timeline/rows';

	let { person }: { person: ShowPersonResource } = $props();

	// keep year labels at least this far apart
	const minTickSpacing = 48;

	let timeline = $derived(buildTimeline(person, new Date()));

	let rowCount = $derived(timeline.groups.reduce((count, group) => count + group.rows.length, 0));

	let track = $state<HTMLElement>();
	let width = $state(0);

	let step = $derived.by(() => {
		const span = timeline.max - timeline.min;
		const available = width || 600;
		return [5, 10, 20, 25, 50, 100, 200].find((s) => (span / s) * minTickSpacing <= available) ?? 500;
	});

	let min = $derived(Math.floor(timeline.min / step) * step);
	let max = $derived(Math.ceil(timeline.max / step) * step);

	let ticks = $derived(Array.from({ length: ((max - min) / step) + 1 }, (_, i) => min + (i * step)));

	let cursor = $state<number | null>(null);

	function x(year: number) {
		return ((year - min) / (max - min)) * 100;
	}

	function point(event: PointerEvent) {
		if (!track) return;

		const rect = track.getBoundingClientRect();
		const fraction = (event.clientX - rect.left) / rect.width;
		cursor = fraction < 0 || fraction > 1 ? null : min + (fraction * (max - min));
	}

	function leave(event: PointerEvent) {
		// keep the cursor where a touch left it
		if (event.pointerType === 'mouse') cursor = null;
	}

	function gradient(row: Row) {
		const length = row.end.to - row.start.from;
		const solidFrom = ((row.start.to - row.start.from) / length) * 100;
		const solidTo = ((row.end.from - row.start.from) / length) * 100;

		return `linear-gradient(to right, transparent, currentColor ${solidFrom}%, currentColor ${solidTo}%, transparent)`;
	}

	function title(row: Row) {
		const lifespan = person.timeline.people.find((l) => l.id === row.person.id)!;

		return [
			lifespan.birth ? `∗︎${formatYears(lifespan.birth)}` : null,
			lifespan.death ? `✝︎${formatYears(lifespan.death)}` : null,
		].filter((y) => y !== null).join(', ');
	}
</script>

{#if rowCount > 1}
	<div class="rounded-lg bg-white p-6 shadow-sm">
		<h2 class="mb-3 text-sm font-medium text-gray-500">{t('people.timeline')}</h2>

		<div
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
				{#each ticks as tick (tick)}
					<div class="absolute inset-y-0 border-l border-gray-100" style:left="{x(tick)}%"></div>
				{/each}
				{#if cursor !== null}
					<div class="absolute inset-y-0 z-1 border-l border-blue-400" style:left="{x(cursor)}%"></div>
				{/if}
			</div>

			{#each timeline.groups as group (group.key)}
				{#if group.label}
					<div class="relative mt-3 text-xs font-medium text-gray-400">{t(group.label)}</div>
				{/if}

				{#each group.rows as row (row.key)}
					{@const age = cursor === null ? null : ageAt(row, cursor)}
					<div class="relative grid grid-cols-1 items-center py-0.5 sm:grid-cols-[var(--label)_1fr]">
						<div class="truncate pr-3 text-sm leading-5" class:italic={row.person.isDead}>
							{#if row.current}
								<span class="font-medium" aria-current="page">{@render name()}</span>
							{:else}
								<a {@attach inertia()} href={route('people.show', row.person)} class="text-blue-700 hover:underline">
									{@render name()}
								</a>
							{/if}
						</div>

						<div class="relative h-5">
							<div
								class={[
									'absolute top-1/2 h-2.5 -translate-y-1/2',
									row.current ? 'text-blue-600' : 'text-gray-400',
									row.start.from === row.start.to && 'rounded-l-full',
									row.end.from === row.end.to && !row.alive && 'rounded-r-full',
								]}
								style:left="{x(row.start.from)}%"
								style:width="{x(row.end.to) - x(row.start.from)}%"
								style:background={gradient(row)}
								title={title(row)}
							></div>

							{#each row.markers as marker (marker.key)}
								<span
									class={[
										'absolute top-1/2 z-2 -translate-1/2 rounded-full bg-white px-1 text-xs leading-4 text-gray-700 ring-1 ring-gray-300',
										marker.type === 'divorce' && 'line-through',
									]}
									style:left="{x(marker.at)}%"
									title="{marker.type === 'divorce' ? t('marriages.divorced') : '∞'} {formatYears(marker.years)}"
								>∞</span>
							{/each}

							{#if age !== null}
								<span
									class={[
										'absolute top-1/2 z-3 -translate-y-1/2 rounded-sm bg-white/85 px-1 text-xs font-medium text-blue-700 tabular-nums',
										cursor! > max - ((max - min) * 0.15) ? '-translate-x-full -ml-1' : 'ml-1',
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
		</div>
	</div>
{/if}
