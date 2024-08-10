export type Activity = {
	description: string;
	causer: string | null;
	datetime: string;
	old: Record<string, unknown> | null;
	attributes: Record<string, unknown> | null;
	new: Record<string, unknown> | null;
};
