export type Letters = {
	family: Letter[];
	last: Letter[];
};

type Letter = {
	letter: string;
	count: number;
};

type PersonCommon = {
	id: number;
	visible: boolean;
	pytlewskiUrl: string | null;
	wielcyUrl: string | null;
	canBeUpdated: boolean;
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
