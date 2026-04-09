<script lang="ts">
	import { route } from 'ziggy-js';
	import type { MarriagePage } from '@/types/resources/marriages';
	import { t } from '@/helpers/translations';
	import { icons } from '@/Components/Icons/Icon.svelte';
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
					hotkey="e"
					icon={icons.editPencil}
					active={activeRoute === 'marriages.edit'}
				/>
			{/if}

			{#if marriage.perm.viewHistory}
				<Item
					name="marriages.menu.edits_history"
					href={route('marriages.history', marriage)}
					hotkey="h"
					icon={icons.time}
					active={activeRoute === 'marriages.history'}
				/>
			{/if}

			{#if !marriage.isTrashed}
				<Item
					name="marriages.menu.delete"
					href={route('marriages.destroy', marriage)}
					visitOptions={{
						method: 'delete',
						confirm: t('marriages.menu.delete_confirm'),
					}}
					icon={icons.trash}
					danger
				/>
			{:else if marriage.perm.viewHistory}
				<Item
					name="marriages.menu.restore"
					href={route('marriages.restore', marriage)}
					visitOptions={{ method: 'patch' }}
					icon={icons.trash}
					danger
				/>
			{/if}
		</div>

		<div class="grow">
			<hr class="my-1 text-gray-200 block xs:hidden md:block">

			<Item
				name="marriages.add_child"
				href={route('people.create', { mother: marriage.woman.id, father: marriage.man.id })}
				icon={icons.addOutline}
			/>

			<hr class="my-1 text-gray-200">

			<Item
				name="marriages.woman"
				href={route('people.show', marriage.woman)}
				hotkey="w"
				icon={icons.viewShow}
			/>

			<Item
				name="marriages.man"
				href={route('people.show', marriage.man)}
				hotkey="m"
				icon={icons.viewShow}
			/>
		</div>
	</div>
</ul>
