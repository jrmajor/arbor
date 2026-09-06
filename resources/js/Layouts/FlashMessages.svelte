<script lang="ts">
	import { page, router } from '@inertiajs/svelte';
	import { flide } from '@/helpers/transitions';
	import FlashMessage from './FlashMessage.svelte';
</script>

{#if page.flash.notification}
	<div class="message" transition:flide>
		<FlashMessage
			{...page.flash.notification}
			ondismiss={() => router.flash('notification', undefined)}
		/>
	</div>
{/if}
