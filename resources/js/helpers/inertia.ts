import { fromAction } from 'svelte/attachments';
import { inertia as inertiaAction } from '@inertiajs/svelte';

export type VisitOptions = NonNullable<Parameters<typeof inertiaAction>[1]>;

export function inertia(args?: VisitOptions) {
	// force-casting args to work around some typescript nonsense
	return fromAction(inertiaAction, () => args!);
}
