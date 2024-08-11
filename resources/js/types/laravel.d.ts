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
		email: string | null;
		canWrite: boolean;
		isSuperAdmin: boolean;
	} | null;

	type FlashData = {
		level: 'error' | 'warning' | 'success';
		message: string;
	};

	interface PaginatedResource<Resource> {
		data: Resource[];
		links: PaginationLinks;
		meta: PaginationMeta;
	}

	interface PaginationLinks {
		first: string;
		last: string;
		prev: string | null;
		next: string | null;
	}

	interface PaginationMeta {
		current_page: number;
		from: 1;
		last_page: 6;
		links: PaginationLink[];
		path: string;
		per_page: number;
		to: number;
		total: number;
	}

	interface PaginationLink {
		active: boolean;
		label: string;
		url: string | null;
	}
}
