<script lang="ts">
	import type { EditMarriageResource } from '@/types/resources/marriages';
	import { t } from	'@/helpers/translations';
	import PageTitle from '@/Components/PageTitle.svelte';
	import Form from './Components/Form.svelte';

	let { manId, womanId }: {
		manId: number | null;
		womanId: number | null;
	} & SharedProps = $props();

	// svelte-ignore state_referenced_locally
	const marriage: EditMarriageResource = {
		id: 0,
		woman: { id: womanId },
		man: { id: manId },
		womanOrder: 1,
		manOrder: 1,
		rite: null,
		firstEventType: null,
		firstEventDateFrom: null,
		firstEventDateTo: null,
		firstEventPlace: null,
		secondEventType: null,
		secondEventDateFrom: null,
		secondEventDateTo: null,
		secondEventPlace: null,
		divorced: false,
		divorceDateFrom: null,
		divorceDateTo: null,
		divorcePlace: null,
	};
</script>

<svelte:head>
	<title>{t('marriages.titles.new_marriage')} - Arbor</title>
</svelte:head>

<PageTitle>
	{t('marriages.add_a_new_marriage')}
</PageTitle>

<Form {marriage} action="create"/>
