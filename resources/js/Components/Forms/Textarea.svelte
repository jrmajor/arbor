<script lang="ts">
	import { formFieldContext } from './contexts';

	let {
		value = $bindable(),
		rows,
	}: {
		value: string | number | null;
		rows: number;
	} = $props();

	const formField = formFieldContext.get();
</script>

<textarea
	id={formField?.id}
	bind:value
	{rows}
	class="form-input w-full min-h-full resize-y"
	class:invalid={formField?.error}
></textarea>
