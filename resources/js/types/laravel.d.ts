interface SharedProps {
	errors: unknown[];
	user: SharedUser;
}

type SharedUser = {
	username: string;
} | null;
