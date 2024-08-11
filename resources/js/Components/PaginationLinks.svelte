<script lang="ts">
	import { inertia } from '@inertiajs/svelte';

	let { meta }: { meta: PaginationMeta } = $props();
</script>

{#if meta.last_page > 1}
	<ul class="flex justify-center lg:justify-start flex-wrap space-x-3 -my-1" role="navigation">
		{#each meta.links as element}
			<li class="py-1">
				{@render elementSnippet(element)}
			</li>
		{/each}
	</ul>
{/if}

{#snippet elementSnippet({ active, label, url: href }: PaginationLink)}
	{#if label === 'previous'}
		{#if meta.current_page === 1}
			<span class="btn-out disabled">&lsaquo;</span>
		{:else}
			<a use:inertia {href} rel="prev" class="btn-out">&lsaquo;</a>
		{/if}
	{/if}

	{#if label === '...'}
		<span class="-m-1">{label}</span>
	{/if}

	{#if !isNaN(parseInt(label))}
		{#if active}
			<span class="btn-out disabled">{label}</span>
		{:else}
			<a use:inertia {href} class="btn-out">{label}</a>
		{/if}
	{/if}

	{#if label === 'next'}
		{#if meta.current_page === meta.last_page}
			<span class="btn-out disabled">&rsaquo;</span>
		{:else}
			<a use:inertia {href} rel="next" class="btn-out">&rsaquo;</a>
		{/if}
	{/if}
{/snippet}
