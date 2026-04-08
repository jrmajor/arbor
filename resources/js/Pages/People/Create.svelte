<script lang="ts">
	import type { EditPersonResource } from '@/types/resources/people';
	import { t } from	'@/helpers/translations';
	import PageTitle from '@/Components/PageTitle.svelte';
	import Form from './Components/Form/Form.svelte';

	let { fatherId, motherId }: {
		fatherId: number | null;
		motherId: number | null;
	} & SharedProps = $props();

	// svelte-ignore state_referenced_locally
	const person: EditPersonResource = {
		id: 0,
		sex: null,
		name: '',
		middleName: null,
		familyName: '',
		lastName: null,
		wielcyId: null,
		pytlewskiId: null,
		birthDateFrom: null,
		birthDateTo: null,
		birthPlace: null,
		isDead: false,
		deathDateFrom: null,
		deathDateTo: null,
		deathPlace: null,
		deathCause: null,
		funeralDateFrom: null,
		funeralDateTo: null,
		funeralPlace: null,
		burialDateFrom: null,
		burialDateTo: null,
		burialPlace: null,
		fatherId,
		motherId,
		sources: [],
	};
</script>

<svelte:head>
	<title>{t('people.titles.new_person')} - Arbor</title>
</svelte:head>

<PageTitle>
	{t('people.add_a_new_person')}
</PageTitle>

<Form {person} action="create"/>
