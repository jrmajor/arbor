import { createContext } from 'svelte';
import { createOptionalContext } from '@/helpers/context';

export const [getFormFieldContext, setFormFieldContext] = createOptionalContext<{
	readonly id: string;
	readonly error: string | null | undefined;
}>();

export const [getRadioGroupContext, setRadioGroupContext] = createContext<{
	readonly id: string;
	value: string | null;
}>();
