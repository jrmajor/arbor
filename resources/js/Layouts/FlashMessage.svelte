<script lang="ts">
	let { level, message, ondismiss }: {
		level: 'error' | 'warning' | 'success';
		message: string;
		ondismiss: () => void;
	} = $props();

	let colors = $derived({
		error: 'bg-red-50 text-red-900',
		warning: 'bg-yellow-50 text-yellow-900',
		success: 'bg-green-50 text-green-900',
	}[level]);

	let iconColor = $derived({
		error: 'text-red-500',
		warning: 'text-yellow-500',
		success: 'text-green-500',
	}[level]);

	let closeColor = $derived({
		error: 'text-red-500 hover:text-red-600 active:text-red-700',
		warning: 'text-yellow-500 hover:text-yellow-600 active:text-yellow-700',
		success: 'text-green-500 hover:text-green-600 active:text-green-700',
	}[level]);
</script>

<div class="flex w-full items-center justify-between rounded-lg p-5 shadow {colors}">
	<div class="flex items-center">
		<svg
			xmlns="http://www.w3.org/2000/svg"
			viewBox="0 0 20 20"
			class="mr-4 size-5 flex-none fill-current {iconColor}"
		>
			{#if level === 'error'}
				<path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zM11.4 10l2.83-2.83-1.41-1.41L10 8.59 7.17 5.76 5.76 7.17 8.59 10l-2.83 2.83 1.41 1.41L10 11.41l2.83 2.83 1.41-1.41L11.41 10z"/>
			{:else if level === 'warning'}
				<path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zM9 5v6h2V5H9zm0 8v2h2v-2H9z"/>
			{:else if level === 'success'}
				<path d="M0 11l2-2 5 5L18 3l2 2L7 18z"/>
			{/if}
		</svg>

		{message}
	</div>

	<button onclick={ondismiss} aria-label="Dismiss">
		<svg
			xmlns="http://www.w3.org/2000/svg"
			viewBox="0 0 20 20"
			class="ml-5 size-4 flex-none fill-current transition-colors {closeColor}"
		>
			<path d="M10 8.586 2.929 1.515 1.515 2.929 8.586 10l-7.071 7.071 1.414 1.414L10 11.414l7.071 7.071 1.414-1.414L11.414 10l7.071-7.071-1.414-1.414L10 8.586z"/>
		</svg>
	</button>
</div>
