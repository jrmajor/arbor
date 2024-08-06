<script lang="ts">
	import type { ShowPersonResource } from '@/types/people';

	let { person }: { person: ShowPersonResource } = $props();
</script>

{#if person.biography}
	<div class="p-6 bg-white rounded-lg shadow space-y-2 break-words">
		{person.biography}
	</div>
{/if}
