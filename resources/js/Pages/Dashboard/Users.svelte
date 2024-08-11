<script lang="ts" context="module">
	export { default as layout } from './Components/Layout/Layout.svelte';
</script>

<script lang="ts">
	import type { UserResource } from '@/types/resources/dashboard';

	let { users }: { users: UserResource[] } & SharedProps = $props();

	function permissionsLabel(permissions: number) {
		return { 0: 'none', 1: 'read', 2: 'write', 3: 'view history', 4: 'admin' }[permissions];
	}
</script>

<svelte:head>
	<title>Users - Arbor</title>
</svelte:head>

<main class="grow md:w-1/2 p-6 bg-white rounded-lg shadow">
	<table>
		<thead>
			<tr>
				<th class="pr-4 text-left">username</th>
				<th class="px-4 text-left">perm.</th>
				<th class="px-4 text-left">email</th>
				<th class="pl-4 text-right">latest login</th>
			</tr>
		</thead>

		<tbody>
			{#each users as user (user.id)}
				<tr>
					<td class="pr-4 pt-2">{user.username}</td>
					<td class="px-4 pt-2">{permissionsLabel(user.permissions)}</td>
					<td class="px-4 pt-2">{user.email ?? 'no email'}</td>
					<td class="pl-4 pt-2 tabular-nums text-right">{user.latestLogin ?? 'never'}</td>
				</tr>
			{/each}
		</tbody>
	</table>
</main>
