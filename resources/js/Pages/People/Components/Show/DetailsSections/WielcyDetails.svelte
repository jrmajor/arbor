<script lang="ts">
	import type { Wielcy } from '@/types/resources/wielcy';
	import { t } from '@/helpers/translations';
	import ExternalRelative from './ExternalRelative.svelte';

	let { wielcy }: { wielcy: Wielcy } = $props();
</script>

<small class="block -mt-0.5 ml-4 leading-tight">
	{#if wielcy.mother || wielcy.father}
		<p class="mt-1.5">{t('people.wielcy.parents')}:</p>
		{#if wielcy.mother}
			<p class="ml-4"><ExternalRelative person={wielcy.mother}/></p>
		{/if}
		{#if wielcy.father}
			<p class="ml-4"><ExternalRelative person={wielcy.father}/></p>
		{/if}
	{/if}
</small>
