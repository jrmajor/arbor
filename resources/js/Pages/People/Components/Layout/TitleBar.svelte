<script lang="ts">
	import { Sex, type PersonPage } from '@/types/people';

	let { person }: { person: PersonPage } = $props();
</script>

<div class="flex items-center">
	<div class="mr-1">
		{#if person.sex === Sex.MALE}
			<svg class="stroke-current stroke-3 size-6" viewBox="0 0 18 32" xmlns="http://www.w3.org/2000/svg">
				<circle cx="9" cy="22.5" r="7.5" fill="none"/>
				<path fill="none" d="M9 15V2M9 2m-8 8L9 2l8 8"/>
			</svg>
		{:else if person.sex === Sex.FEMALE}
			<svg class="stroke-current stroke-3 size-6" viewBox="0 0 18 32" xmlns="http://www.w3.org/2000/svg">
				<circle cx="9" cy="9.5" r="7.5" fill="none"/>
				<path fill="none" d="M9 17V30M2 24H16"/>
			</svg>
		{/if}
	</div>

	<div>
		<span class:italic={person.isDead}>
			<span class="font-normal">{person.name}</span>
			{#if person.lastName}
				{person.lastName} ({person.familyName})
			{:else}
				{person.familyName}
			{/if}
		</span>

		<small class="text-lg">
			[№{person.id}]
		</small>
	</div>
</div>
