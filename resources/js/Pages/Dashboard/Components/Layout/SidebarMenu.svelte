<script lang="ts">
	import { route } from 'ziggy-js';
	import { icons } from '@/Components/Icons/Icon.svelte';
	import Item from '@/Components/SidebarMenu/Item.svelte';

	let { activeRoute }: { activeRoute: string } = $props();
</script>

<ul>
	<div class="flex flex-col xs:flex-row md:flex-col">
		<div class="grow">
			<Item
				name="misc.dashboard.users"
				href={route('dashboard.users')}
				hotkey="u"
				icon={icons.userGroup}
				active={activeRoute === 'dashboard.users'}
			/>

			<Item
				name="misc.dashboard.activity_log"
				href={route('dashboard.activityLog')}
				hotkey="a"
				icon={icons.heartbeat}
				active={activeRoute === 'dashboard.activityLog'}
			/>

			<Item
				name="misc.dashboard.reports"
				href={route('dashboard.reports')}
				hotkey="r"
				icon={icons.exclamationOutline}
				active={activeRoute === 'dashboard.reports'}
			/>
		</div>
	</div>
</ul>
