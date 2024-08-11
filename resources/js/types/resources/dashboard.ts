export type UserResource = {
	id: number;
	username: string;
	email: string | null;
	permissions: number;
	latestLogin: string | null;
};

export type ActivityResource = {
	id: number;
	logName: string;
	description: string;
	subjectId: number;
	causer: string | null;
	datetime: string;
};
