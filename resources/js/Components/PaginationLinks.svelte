<script lang="ts">
	import Button from '@/Components/Primitives/Button.svelte';

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
		{@const disabled = meta.current_page === 1}
		<Button type="link" {href} rel="prev" {disabled} outline>&lsaquo;</Button>
	{/if}

	{#if label === '...'}
		<span class="-m-1">{label}</span>
	{/if}

	{#if !isNaN(parseInt(label))}
		<Button type="link" {href} disabled={active} outline>{label}</Button>
	{/if}

	{#if label === 'next'}
		{@const disabled = meta.current_page === meta.last_page}
		<Button type="link" {href} rel="next" {disabled} outline>&rsaquo;</Button>
	{/if}
{/snippet}
