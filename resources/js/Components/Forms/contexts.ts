import { createContext } from '@/helpers/context';

export const formFieldContext = createContext<{
	readonly id: string;
	readonly error: string | null | undefined;
}>();

export const radioGroupContext = createContext<{
	readonly id: string;
	value: string | null;
}>();
