<script lang="ts">
	import { route } from 'ziggy-js';
	import { Sex, type PersonPage } from '@/types/people';
	import { t } from '@/helpers/translations';
	import Item from '@/Components/SidebarMenu/Item.svelte';

	let { person, activePage }: {
		person: PersonPage;
		activePage: string;
	} = $props();
</script>

<ul class="uppercase">
	<div class="flex flex-col xs:flex-row md:flex-col">
		<div class="grow">
			{#if !person.isTrashed}
				<Item
					name="people.menu.overview"
					href={route('people.show', person)}
					active={activePage === 'show'}
				>
					<path d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
				</Item>

				<Item
					name="people.menu.edit_person"
					href={route('people.edit', person)}
					active={activePage === 'edit'}
				>
					<path d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
				</Item>

				<Item
					name="people.menu.edit_biography"
					href={route('people.biography.edit', person)}
					active={activePage === 'biography'}
				>
					<path d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
				</Item>
			{/if}

			{#if person.perm.viewHistory}
				<Item
					name="people.menu.edits_history"
					href={route('people.history', person)}
					active={activePage === 'history'}
				>
					<path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-1-7.59V4h2v5.59l3.95 3.95-1.41 1.41L9 10.41z"/>
				</Item>
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
					danger={!person.isVisible}
				>
					{#if person.isVisible}
						<path d="M17 16a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4.01V4a1 1 0 0 1 1-1 1 1 0 0 1 1 1v6h1V2a1 1 0 0 1 1-1 1 1 0 0 1 1 1v8h1V1a1 1 0 1 1 2 0v9h1V2a1 1 0 0 1 1-1 1 1 0 0 1 1 1v13h1V9a1 1 0 0 1 1-1h1v8z"/>
					{:else}
						<path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm2-2.25a8 8 0 0 0 4-2.46V9a2 2 0 0 1-2-2V3.07a7.95 7.95 0 0 0-3-1V3a2 2 0 0 1-2 2v1a2 2 0 0 1-2 2v2h3a2 2 0 0 1 2 2v5.75zm-4 0V15a2 2 0 0 1-2-2v-1h-.5A1.5 1.5 0 0 1 4 10.5V8H2.25A8.01 8.01 0 0 0 8 17.75z"/>
					{/if}
				</Item>
			{/if}

			{#if !person.isTrashed}
				<Item
					name="people.menu.delete"
					href={route('people.destroy', person)}
					visitOptions={{
						method: 'delete',
						confirm: t('people.menu.delete_confirm'),
					}}
					danger
				>
					<path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
				</Item>
			{:else if person.perm.viewHistory}
				<Item
					name="people.menu.restore"
					href={route('people.restore', person)}
					visitOptions={{ method: 'patch' }}
					danger
				>
					<path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
				</Item>
			{/if}

			{#if !person.isTrashed}
				<hr class="my-1">

				<Item
					name="marriages.add_a_new_marriage"
					href={route('marriages.create', { [person.sex === Sex.FEMALE ? 'woman' : 'man']: person.id })}
				>
					<path d="M11 9h4v2h-4v4H9v-4H5V9h4V5h2v4zm-1 11a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/>
				</Item>
			{/if}
		</div>
	</div>
</ul>
