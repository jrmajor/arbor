<script lang="ts">
	import { onMount } from 'svelte';
	import { slide } from 'svelte/transition';
	import { router } from '@inertiajs/svelte';
	import Message from './FlashMessage.svelte';

	let { flash }: { flash: FlashMessage | null } = $props();

	type MessageWithId = FlashMessage & { id: number };

	let messages: MessageWithId[] = $state(flash ? [addId(flash)] : []);

	onMount(() => {
		router.on('finish', () => {
			if (flash) messages.push(addId(flash));
		});
	});

	function addId(message: FlashMessage) {
		return { ...message, id: Math.floor(Math.random() * (10 ** 8)) };
	}
</script>

<div class="{messages.length ? 'mb-6' : 'mb-0'} flex flex-col gap-3">
	{#each messages as message, i (message.id)}
		<div class="message" transition:slide>
			<Message {...message} ondismiss={() => messages.splice(i, 1)}/>
		</div>
	{/each}
</div>
