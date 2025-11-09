<script lang="ts">
	import { getFormFieldContext } from './contexts';

	let {
		value = $bindable(),
		rows,
	}: {
		value: string | number | null;
		rows: number;
	} = $props();

	const formField = getFormFieldContext();
</script>

<textarea
	id={formField?.id}
	bind:value
	{rows}
	class="form-input field-sizing-content w-full resize-y"
	class:invalid={formField?.error}
	style:min-height="{rows * 1.5}rem"
></textarea>
