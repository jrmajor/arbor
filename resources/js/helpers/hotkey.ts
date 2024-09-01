import { install, uninstall } from '@github/hotkey';

export function hotkey(node: HTMLElement, hotkey: string) {
	install(node, hotkey);

	return {
		update: () => {
			uninstall(node);
			install(node, hotkey);
		},
		destroy: () => uninstall(node),
	};
}
