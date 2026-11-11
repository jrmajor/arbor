<script lang="ts">
	import { router } from '@inertiajs/svelte';
	import { route } from 'ziggy-js';
	import PersonPicker from '@/Components/Forms/PersonPicker.svelte';

	let { personId, value = null }: { personId: number, value?: number | null } = $props();

	// svelte-ignore state_referenced_locally
	let selected = $state(value);

	$effect(() => {
		if (selected !== null && selected !== value) {
			router.get(route('people.relationship', { person: personId, to: selected }));
		}
	});
</script>

<PersonPicker bind:value={selected} initialValue={value} sex={null} nullable/>
