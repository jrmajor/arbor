<script lang="ts">
	import type { ShowPersonResource } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import RelationshipPicker from '../../Relationship/RelationshipPicker.svelte';

	let { person }: { person: ShowPersonResource } = $props();
</script>

<div class="p-6 bg-white rounded-lg shadow-sm">
	<p class="mb-2 text-sm text-gray-600">{t('relationship.check_with')}</p>
	<RelationshipPicker personId={person.id}/>
</div>
