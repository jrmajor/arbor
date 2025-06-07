<script lang="ts" module>
	export { default as layout } from './Components/Layout/Layout.svelte';
</script>

<script lang="ts">
	import type { EditPersonResource, PersonPage } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import Form from './Components/Form/Form.svelte';

	let { person }: { person: EditPersonResource & PersonPage } & SharedProps = $props();
</script>

<svelte:head>
	<title>{person.simpleName} - {t('people.titles.editing')} - Arbor</title>
</svelte:head>

<Form {person} action="edit"/>
