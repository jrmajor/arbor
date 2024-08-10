<script lang="ts">
	import type { EditPersonResource } from '@/types/people';
	import { t } from	'@/helpers/translations';
	import Form from './Components/Form/Form.svelte';

	let { fatherId, motherId }: {
		fatherId: number | null;
		motherId: number | null;
	} & SharedProps = $props();

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

<h1 class="mb-3 leading-none text-3xl font-medium">
	{t('people.add_a_new_person')}
</h1>

<Form {person} action="create"/>
