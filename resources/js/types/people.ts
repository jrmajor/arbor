import type { Pytlewski } from './pytlewski';

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
	perm: {
		update: boolean;
	};
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
	perm: {
		update: boolean;
		changeVisibility: boolean;
		delete: boolean;
		restore: boolean;
		viewHistory: boolean;
	};
};

export type ShowPersonResource = PersonPage & {
	middleName: string | null;
	birthDate: string | null;
	birthPlace: string | null;
	deathDate: string | null;
	deathPlace: string | null;
	deathCause: string | null;
	funeralDate: string | null;
	funeralPlace: string | null;
	burialDate: string | null;
	burialPlace: string | null;
	father: (Person & { father: Person | null; mother: Person | null }) | null;
	mother: (Person & { father: Person | null; mother: Person | null }) | null;
	siblings: Person[];
	siblingsFather: Person[];
	siblingsMother: Person[];
	marriages: Marriage[];
	children: Person[];
	age: {
		current: number | null;
		prettyCurrent: string | null;
		atDeath: number | null;
		prettyAtDeath: string | null;
		estimatedBirthDate?: number | null;
		estimatedBirthDateError?: number | null;
	};
	pytlewskiId: number | null;
	pytlewskiUrl: string | null;
	pytlewski: Pytlewski | null;
	wielcy: {
		id: string;
		url: string;
		name: string | null;
	};
	biography: string;
	sources: string[];
};

type Marriage = {
	id: number;
	order: number | null;
	rite: string | null;
	firstEvent?: {
		type: string | null;
		date: string | null;
		place: string | null;
	};
	secondEvent?: {
		type: string | null;
		date: string | null;
		place: string | null;
	};
	divorced: boolean;
	divorceDate: string | null;
	divorcePlace: string | null;
	partner: Person;
	perm: {
		view: boolean;
		update: boolean;
	};
};
