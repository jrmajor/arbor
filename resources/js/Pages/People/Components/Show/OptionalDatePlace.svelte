<script lang="ts">
	let { date, place, prefix = null, multiline = false }: {
		date: string | null;
		place: string | null;
		prefix?: string | null;
		multiline?: boolean;
	} = $props();

	let prefixWithColon = $derived(prefix ? `${prefix}:` : '');
</script>

{#if place !== null && date !== null}
	{#if multiline}
		<p>{date}</p>
		<p>{place}</p>
	{:else}
		<p>{prefixWithColon} {place}, {date}</p>
	{/if}
{:else if place !== null}
	<p>{prefixWithColon} {place}</p>
{:else if date !== null}
	<p>{prefixWithColon} {date}</p>
{:else if prefix}
	<p>{prefix}</p>
{/if}
