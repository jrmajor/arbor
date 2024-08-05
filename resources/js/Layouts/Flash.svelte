<script lang="ts">
	let { level, message }: {
		level: 'error' | 'warning' | 'success';
		message: string;
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
</script>

<div class="w-full rounded-lg shadow p-5 flex items-center mb-3 {colors}">
	<svg
		xmlns="http://www.w3.org/2000/svg"
		viewBox="0 0 20 20"
		class="flex-none size-5 mr-5 fill-current {iconColor}"
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
