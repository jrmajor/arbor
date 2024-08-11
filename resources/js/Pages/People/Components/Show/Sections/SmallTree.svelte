<script lang="ts">
	import type { ShowPersonResource } from '@/types/resources/people';
	import Name from '@/Components/Name.svelte';

	let { person }: { person: ShowPersonResource } = $props();
</script>

{#if person.father || person.mother}
	<div class="rounded-lg bg-white p-6 shadow">
		<div class="text-center">
			<div class="flex w-full text-sm">
				<div class="flex w-1/2 flex-col justify-center md:flex-row">
					{#if person.father}
						<div class="w-full md:w-1/2 md:text-right">
							{#if person.father.father}
								<Name person={person.father.father} showYears={false}/>
							{/if}
						</div>
						{#if person.father.father && person.father.mother}
							<div class="mx-1">+</div>
						{/if}
						<div class="w-full md:w-1/2 md:text-left">
							{#if person.father.mother}
								<Name person={person.father.mother} showYears={false}/>
							{/if}
						</div>
					{/if}
				</div>
				<div class="flex w-1/2 flex-col justify-center md:flex-row">
					{#if person.mother}
						<div class="w-full md:w-1/2 md:text-right">
							{#if person.mother.father}
								<Name person={person.mother.father} showYears={false}/>
							{/if}
						</div>
						{#if person.mother.father && person.mother.mother}
							<div class="mx-1">+</div>
						{/if}
						<div class="w-full md:w-1/2 md:text-left">
							{#if person.mother.mother}
								<Name person={person.mother.mother} showYears={false}/>
							{/if}
						</div>
					{/if}
				</div>
			</div>

			<div class="flex w-full">
				<div class="w-1/2">
					{#if person.father}
						{#if person.father.father || person.father.mother}
							↓
						{/if}
					{/if}
				</div>
				<div class="w-1/2">
					{#if person.mother}
						{#if person.mother.father || person.mother.mother}
							↓
						{/if}
					{/if}
				</div>
			</div>

			<div class="flex w-full">
				<div class="w-1/2">
					{#if person.father}
						<Name person={person.father}/>
					{/if}
				</div>
				{#if person.father && person.mother}
					<div class="mx-1">+</div>
				{/if}
				<div class="w-1/2">
					{#if person.mother}
						<Name person={person.mother}/>
					{/if}
				</div>
			</div>

			{#if person.father || person.mother}
				<div class="w-full">
					↓
				</div>
			{/if}

			<div class="w-full">
				<Name person={{ ...person, visible: true, wielcyUrl: person.wielcy?.url }}/>
			</div>
		</div>
	</div>
{/if}
