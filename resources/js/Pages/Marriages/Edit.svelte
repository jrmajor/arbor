<script lang="ts" module>
	export { default as layout } from './Components/Layout/Layout.svelte';
</script>

<script lang="ts">
	import type { MarriagePage, EditMarriageResource } from '@/types/resources/marriages';
	import { t } from '@/helpers/translations';
	import Form from './Components/Form.svelte';

	let { marriage }: { marriage: EditMarriageResource & MarriagePage } & SharedProps = $props();
</script>

<svelte:head>
	<title>{t('marriages.titles.editing_marriage')} - Arbor</title>
</svelte:head>

<Form {marriage} action="edit"/>
