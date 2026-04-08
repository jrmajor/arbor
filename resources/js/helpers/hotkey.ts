import type { Attachment } from 'svelte/attachments';
import { install, uninstall } from '@github/hotkey';

export function hotkey(hotkey: string): Attachment<HTMLElement> {
	return (el) => {
		install(el, hotkey);
		return () => uninstall(el);
	};
}
