interface SharedProps {
	errors: unknown[];
	flash: FlashData | null;
	user: SharedUser;
}

type SharedUser = {
	username: string;
} | null;

type FlashData = {
	level: 'error' | 'warning' | 'success';
	message: string;
};
