import { createContext, getContext, setContext } from 'svelte';
import type { Writable } from 'svelte/store';

export function createOptionalContext<T>(): [() => T | null, (context: T) => T] {
	const key = {};

	return [
		() => getContext(key),
		(context) => setContext(key, context),
	];
}

export const [getAuthLayoutTitle, setAuthLayoutTitle] = createContext<Writable<string | null>>();
