export type UserResource = {
	id: number;
	username: string;
	email: string | null;
	permissions: number;
	latestLogin: string | null;
};
