import type { RouteList } from 'ziggy-js';

declare global {
	interface SharedProps {
		errors: Record<string, string>;
		flash: FlashData | null;
		activeRoute: keyof RouteList;
		user: SharedUser;
	}

	type SharedUser = {
		username: string;
		email: string;
		canWrite: boolean;
		isSuperAdmin: boolean;
	} | null;

	type FlashData = {
		level: 'error' | 'warning' | 'success';
		message: string;
	};
}
