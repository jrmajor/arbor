export type MarriagePage = {
	id: number;
	isTrashed: boolean;
	man: {
		id: number;
		name: string;
		familyName: string;
		isDead: boolean;
	};
	woman: {
		id: number;
		name: string;
		familyName: string;
		isDead: boolean;
	};
	perm: {
		viewHistory: boolean;
	};
};
