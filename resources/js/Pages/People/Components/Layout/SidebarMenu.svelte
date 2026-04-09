<script lang="ts">
	import { route } from 'ziggy-js';
	import { Sex, type PersonPage } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import { icons } from '@/Components/Icons/Icon.svelte';
	import Item from '@/Components/SidebarMenu/Item.svelte';

	let { person, activeRoute }: {
		person: PersonPage;
		activeRoute: string;
	} = $props();
</script>

<ul>
	<div class="flex flex-col xs:flex-row md:flex-col">
		<div class="grow">
			{#if !person.isTrashed}
				<Item
					name="people.menu.overview"
					href={route('people.show', person)}
					hotkey="v"
					icon={icons.viewShow}
					active={activeRoute === 'people.show'}
				/>

				<Item
					name="people.menu.edit_person"
					href={route('people.edit', person)}
					hotkey="e"
					icon={icons.editPencil}
					active={activeRoute === 'people.edit'}
				/>

				<Item
					name="people.menu.edit_biography"
					href={route('people.biography.edit', person)}
					hotkey="b"
					icon={icons.editPencil}
					active={activeRoute === 'people.biography.edit'}
				/>
			{/if}

			{#if person.perm.viewHistory}
				<Item
					name="people.menu.edits_history"
					href={route('people.history', person)}
					hotkey="h"
					icon={icons.time}
					active={activeRoute === 'people.history'}
				/>
			{/if}
		</div>

		<div class="grow">
			{#if person.perm.changeVisibility && !person.isTrashed}
				<Item
					name={person.isVisible ? 'people.menu.make_invisible' : 'people.menu.make_visible'}
					href={route('people.changeVisibility', person)}
					visitOptions={{
						method: 'put',
						data: { visibility: person.isVisible ? '0' : '1' },
					}}
					icon={person.isVisible ? icons.handStop : icons.globe}
					danger={!person.isVisible}
				/>
			{/if}

			{#if !person.isTrashed}
				<Item
					name="people.menu.delete"
					href={route('people.destroy', person)}
					visitOptions={{
						method: 'delete',
						confirm: t('people.menu.delete_confirm'),
					}}
					icon={icons.trash}
					danger
				/>
			{:else if person.perm.viewHistory}
				<Item
					name="people.menu.restore"
					href={route('people.restore', person)}
					visitOptions={{ method: 'patch' }}
					icon={icons.trash}
					danger
				/>
			{/if}

			{#if !person.isTrashed}
				<hr class="my-1 text-gray-200">

				<Item
					name="marriages.add_a_new_marriage"
					href={route('marriages.create', { [person.sex === Sex.FEMALE ? 'woman' : 'man']: person.id })}
					icon={icons.addOutline}
				/>
			{/if}
		</div>
	</div>
</ul>
