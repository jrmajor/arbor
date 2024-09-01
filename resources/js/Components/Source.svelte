<script lang="ts">
	let { html }: { html: string } = $props();
</script>

<small>
	{@html html}
</small>

<style lang="postcss">
	small {
		@apply text-black;

		& :global(a) {
			@apply text-blue-700 transition-colors duration-100 hover:text-blue-800 focus:text-blue-800;
		}
	}
</style>
