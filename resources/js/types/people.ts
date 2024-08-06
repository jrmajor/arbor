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

export enum Sex {
	MALE = 'xy',
	FEMALE = 'xx',
}

export type PersonPage = {
	id: number;
	sex: Sex;
	simpleName: string;
	name: string;
	familyName: string;
	lastName: string | null;
	isDead: boolean;
	isTrashed: boolean;
	isVisible: boolean;
	canBeUpdated: boolean;
	canHaveVisibilityChanged: boolean;
	canBeDeleted: boolean;
	canBeRestored: boolean;
	canHaveHistoryViewed: boolean;
};
