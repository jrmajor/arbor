<script lang="ts">
	import Icon, { icons } from '@/Components/Icons/Icon.svelte';

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

	let icon = $derived({
		error: icons.error,
		warning: icons.warning,
		success: icons.success,
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

<div class="flex w-full items-center justify-between gap-5 rounded-lg p-5 shadow-sm {colors}">
	<div class="flex items-center gap-4">
		<Icon icon={icon} class="size-5 flex-none {iconColor}"/>
		{message}
	</div>

	<button onclick={ondismiss} aria-label="Dismiss">
		<svg
			xmlns="http://www.w3.org/2000/svg"
			viewBox="0 0 20 20"
			class="size-4 flex-none fill-current transition-colors {closeColor}"
		>
			<path d="M10 8.586 2.929 1.515 1.515 2.929 8.586 10l-7.071 7.071 1.414 1.414L10 11.414l7.071 7.071 1.414-1.414L11.414 10l7.071-7.071-1.414-1.414L10 8.586z"/>
		</svg>
	</button>
</div>
