<script lang="ts">
	import { type Snippet } from 'svelte';

	let { children }: { children: Snippet } = $props();
</script>

<dl>{@render children()}</dl>

<style>
	@reference '$style';

	dl {
		display: grid;
		grid-template-columns: 1fr;

		@media (width >= theme(--breakpoint-sm)) {
			grid-template-columns: min-content 1fr;
		}
	}

	dl :global(dt) {
		@apply font-bold sm:relative sm:overflow-hidden sm:whitespace-nowrap;

		@media (width >= theme(--breakpoint-sm)) {
			width: calc(100% + 1.5rem);
		}

		&::after {
			margin-top: 0.67rem;
			font-size: 0.4rem;
			@apply absolute ml-1.5 font-normal hidden sm:inline;
			content: " . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .";
		}
	}

	dl :global(dd) {
		@apply pl-4 sm:ml-6;

		&:not(:last-child) {
			@apply pb-1;
		}
	}
</style>
