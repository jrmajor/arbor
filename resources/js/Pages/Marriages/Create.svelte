<script lang="ts">
	import type { EditMarriageResource } from '@/types/resources/marriages';
	import { t } from	'@/helpers/translations';
	import Form from './Components/Form.svelte';

	let { manId, womanId }: {
		manId: number | null;
		womanId: number | null;
	} & SharedProps = $props();

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

<h1 class="mb-3 leading-none text-3xl font-medium">
	{t('marriages.add_a_new_marriage')}
</h1>

<Form {marriage} action="create"/>
