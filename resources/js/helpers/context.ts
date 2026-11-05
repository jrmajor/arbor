import { createContext } from 'svelte';
import type { Writable } from 'svelte/store';

export const [getAuthLayoutTitle, setAuthLayoutTitle] = createContext<Writable<string | null>>();
