<script lang="ts" module>
	export { default as layout } from './Components/Layout/Layout.svelte';
</script>

<script lang="ts">
	import type { ShowPersonResource } from '@/types/resources/people';
	import Details from './Components/Show/Sections/Details.svelte';
	import Biography from './Components/Show/Sections/Biography.svelte';
	import SmallTree from './Components/Show/Sections/SmallTree.svelte';

	let { person }: { person: ShowPersonResource } & SharedProps = $props();
</script>

<svelte:head>
	<title>{person.simpleName} - Arbor</title>
</svelte:head>

<Details {person}/>
<Biography {person}/>
<SmallTree {person}/>
