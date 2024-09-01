<script lang="ts">
	import { formFieldContext } from './contexts';

	let { value = $bindable() }: { value: string | null } = $props();

	const formField = formFieldContext.get();
</script>

<input
	id={formField?.id}
	class="form-input w-full"
	class:invalid={formField?.error}
	bind:value
/>
