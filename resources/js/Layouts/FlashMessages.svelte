<script lang="ts">
	import { onMount } from 'svelte';
	import { router } from '@inertiajs/svelte';
	import { flide } from '@/helpers/transitions';
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

<div>
	{#each messages as message, i (message.id)}
		<div class="message" transition:flide>
			<Message {...message} ondismiss={() => messages.splice(i, 1)}/>
		</div>
	{/each}
</div>

<style lang="postcss">
	.message {
		@apply mb-3;
	}
	.message:last-child {
		@apply mb-6;
	}
</style>
