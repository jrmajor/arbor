<script lang="ts">
	import { onMount } from 'svelte';
	import { SvelteSet } from 'svelte/reactivity';
	import { router } from '@inertiajs/svelte';
	import { flide } from '@/helpers/transitions';
	import Message from './FlashMessage.svelte';

	let { flash }: { flash: FlashMessage | null } = $props();

	// svelte-ignore state_referenced_locally
	let displayedIds = new SvelteSet(flash ? [flash.id] : []);
	// svelte-ignore state_referenced_locally
	let messages = $state(flash ? [flash] : []);

	onMount(() => {
		router.on('finish', () => {
			messages = [];
			if (!flash) return;
			if (displayedIds.has(flash.id)) return;
			displayedIds.add(flash.id);
			messages.push(flash);
		});
	});
</script>

<div class="flex flex-col gap-3" class:hidden={!messages.length}>
	{#each messages as message, i (message.id)}
		<div class="message" transition:flide>
			<Message {...message} ondismiss={() => messages.splice(i, 1)}/>
		</div>
	{/each}
</div>
