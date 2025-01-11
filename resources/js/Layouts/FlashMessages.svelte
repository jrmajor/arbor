<script lang="ts">
	import { onMount } from 'svelte';
	import { SvelteSet } from 'svelte/reactivity';
	import { router } from '@inertiajs/svelte';
	import { flide } from '@/helpers/transitions';
	import Message from './FlashMessage.svelte';

	let { flash }: { flash: FlashMessage | null } = $props();

	let displayedIds = new SvelteSet(flash ? [flash.id] : []);
	let messages = $state(flash ? [flash] : []);

	onMount(() => {
		router.on('finish', () => {
			if (!flash) return;
			if (displayedIds.has(flash.id)) return;
			displayedIds.add(flash.id);
			messages.push(flash);
		});
	});
</script>

<div>
	{#each messages as message, i (message.id)}
		<div class="message" transition:flide>
			<Message {...message} ondismiss={() => messages.splice(i, 1)}/>
		</div>
	{/each}
</div>

<style>
	@reference '$style';

	.message {
		@apply mb-3;
	}
	.message:last-child {
		@apply mb-6;
	}
</style>
