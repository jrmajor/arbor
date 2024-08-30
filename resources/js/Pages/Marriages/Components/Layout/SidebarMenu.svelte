<script lang="ts">
	import { route } from 'ziggy-js';
	import type { MarriagePage } from '@/types/resources/marriages';
	import { t } from '@/helpers/translations';
	import Item from '@/Components/SidebarMenu/Item.svelte';

	let { marriage, activeRoute }: {
		marriage: MarriagePage;
		activeRoute: string;
	} = $props();
</script>

<ul>
	<div class="flex flex-col xs:flex-row md:flex-col">
		<div class="grow">
			{#if !marriage.isTrashed}
				<Item
					name="marriages.menu.edit_marriage"
					href={route('marriages.edit', marriage)}
					active={activeRoute === 'marriages.edit'}
				>
					<path d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
				</Item>
			{/if}

			{#if marriage.perm.viewHistory}
				<Item
					name="marriages.menu.edits_history"
					href={route('marriages.history', marriage)}
					active={activeRoute === 'marriages.history'}
				>
					<path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-1-7.59V4h2v5.59l3.95 3.95-1.41 1.41L9 10.41z"/>
				</Item>
			{/if}

			{#if !marriage.isTrashed}
				<Item
					name="marriages.menu.delete"
					href={route('marriages.destroy', marriage)}
					visitOptions={{
						method: 'delete',
						confirm: t('marriages.menu.delete_confirm'),
					}}
					danger
				>
					<path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
				</Item>
			{:else if marriage.perm.viewHistory}
				<Item
					name="marriages.menu.restore"
					href={route('marriages.restore', marriage)}
					visitOptions={{ method: 'patch' }}
					danger
				>
					<path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
				</Item>
			{/if}
		</div>

		<div class="grow">
			<hr class="my-1 text-gray-200 block xs:hidden md:block">

			<Item
				name="marriages.add_child"
				href={route('people.create', { mother: marriage.woman.id, father: marriage.man.id })}
			>
				<path d="M11 9h4v2h-4v4H9v-4H5V9h4V5h2v4zm-1 11a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/>
			</Item>

			<hr class="my-1 text-gray-200">

			<Item
				name="marriages.woman"
				href={route('people.show', marriage.woman)}
			>
				<path d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
			</Item>

			<Item
				name="marriages.man"
				href={route('people.show', marriage.man)}
			>
				<path d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
			</Item>
		</div>
	</div>
</ul>
