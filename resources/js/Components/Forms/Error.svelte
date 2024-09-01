<script lang="ts">
	let { error }: { error: string | null | undefined } = $props();
</script>

{#if error}
	<div class="w-full leading-none mt-1">
		<small class="text-red-500">{error}</small>
	</div>
{/if}
