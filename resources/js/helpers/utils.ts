import type { Action } from 'svelte/action';

export function randomId() {
	return `${Math.floor(Math.random() * (10 ** 8))}`;
}

// eslint-disable-next-line func-style
export const voidAction: Action<HTMLElement, any> = () => ({});
