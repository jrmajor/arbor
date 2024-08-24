<script lang="ts">
	let { text }: { text: string } = $props();
</script>

{#each text.split('\n\n') as paragraph}
	<p>{paragraph}</p>
{/each}
