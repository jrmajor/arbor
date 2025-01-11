<script lang="ts">
	import type { ShowPersonResource } from '@/types/resources/people';
	import ParagraphsFromNewlines from '@/Components/ParagraphsFromNewlines.svelte';

	let { person }: { person: ShowPersonResource } = $props();
</script>

{#if person.biography}
	<div class="p-6 bg-white rounded-lg shadow-sm space-y-2 break-words">
		<ParagraphsFromNewlines text={person.biography}/>
	</div>
{/if}
