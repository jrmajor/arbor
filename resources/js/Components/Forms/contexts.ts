import { createContext } from 'svelte';

const [baseGetFormFieldContext, setFormFieldContext, hasFormFieldContext] = createContext<{
	readonly id: string;
	readonly error: string | null | undefined;
}>();

function getFormFieldContext() {
	return hasFormFieldContext() ? baseGetFormFieldContext() : null;
}

export { getFormFieldContext, setFormFieldContext };

export const [getRadioGroupContext, setRadioGroupContext] = createContext<{
	readonly id: string;
	value: string | null;
}>();
