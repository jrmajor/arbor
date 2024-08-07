import { getContext, setContext } from 'svelte';
import { type Writable } from 'svelte/store';

interface Context<T> {
	get: () => T;
	set: (context: T) => T;
}

export function createContext<T>(): Context<T> {
	const key = {};

	return {
		get: () => getContext<T>(key),
		set: (context: T) => setContext(key, context),
	};
}

export const authLayoutTitle = createContext<Writable<string | null>>();
