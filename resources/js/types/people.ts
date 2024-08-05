type PersonCommon = {
	id: number;
	visible: boolean;
	canBeEdited: boolean;
	pytlewskiUrl: string | null;
	wielcyUrl: string | null;
};

type VisiblePerson = PersonCommon & {
	visible: true;
	name: string;
	familyName: string;
	lastName: string | null;
	isDead: boolean;
	birthYear: number | null;
	deathYear: number | null;
};

type HiddenPerson = PersonCommon & {
	visible: false;
};

export type Person = VisiblePerson | HiddenPerson;
