import { getContext, setContext } from 'svelte';

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
