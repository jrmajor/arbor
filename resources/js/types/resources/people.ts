import type { Pytlewski } from './pytlewski';
import type { Wielcy } from './wielcy';

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
	birthYears: YearRange | null;
	deathYears: YearRange | null;
};

type HiddenPerson = PersonCommon & {
	visible: false;
};

export type Person = VisiblePerson | HiddenPerson;

type PersonWithParentIds = Person & {
	fatherId: number | null;
	motherId: number | null;
};

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
	birthYear: number | null;
	birthYears: YearRange | null;
	birthDate: string | null;
	birthPlace: string | null;
	deathYear: number | null;
	deathYears: YearRange | null;
	deathDate: string | null;
	deathPlace: string | null;
	deathCause: string | null;
	funeralDate: string | null;
	funeralPlace: string | null;
	burialDate: string | null;
	burialPlace: string | null;
	father: (Person & { father: Person | null, mother: Person | null }) | null;
	mother: (Person & { father: Person | null, mother: Person | null }) | null;
	siblings: Person[];
	siblingsFather: PersonWithParentIds[];
	siblingsMother: PersonWithParentIds[];
	marriages: Marriage[];
	children: PersonWithParentIds[];
	siblingsBefore: number;
	otherParents: Person[];
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
	wielcyId: string | null;
	wielcyUrl: string | null;
	wielcy: Wielcy | null;
	biography: string;
	sources: string[];
};

export type YearRange = [from: number, to: number];

export type EditPersonResource = {
	id: number;
	sex: Sex | null;
	name: string;
	middleName: string | null;
	familyName: string;
	lastName: string | null;
	wielcyId: string | null;
	pytlewskiId: number | null;
	birthDateFrom: string | null;
	birthDateTo: string | null;
	birthPlace: string | null;
	isDead: boolean;
	deathDateFrom: string | null;
	deathDateTo: string | null;
	deathPlace: string | null;
	deathCause: string | null;
	funeralDateFrom: string | null;
	funeralDateTo: string | null;
	funeralPlace: string | null;
	burialDateFrom: string | null;
	burialDateTo: string | null;
	burialPlace: string | null;
	fatherId: number | null;
	motherId: number | null;
	sources: string[];
};

type MarriageCommon = {
	id: number;
	order: number | null;
	partner: Person;
	perm: {
		update: boolean;
	};
};

type VisibleMarriage = MarriageCommon & {
	visible: true;
	rite: string | null;
	firstEvent?: MarriageEvent;
	secondEvent?: MarriageEvent;
	divorced: boolean;
	divorceDate: string | null;
	divorcePlace: string | null;
	startYears: YearRange | null;
	divorceYears: YearRange | null;
};

type HiddenMarriage = MarriageCommon & {
	visible: false;
};

type Marriage = VisibleMarriage | HiddenMarriage;

type MarriageEvent = {
	type: string | null;
	date: string | null;
	place: string | null;
};

export enum Sex {
	MALE = 'xy',
	FEMALE = 'xx',
}

export type Kinship = { type: string } & Record<string, string | number | boolean>;

export type RelationshipStep = {
	person: Person;
	// how this person is reached from the previous one
	step: 'parent' | 'child' | 'spouse' | null;
	sex: Sex | null;
};

export type Relationship = {
	blood: boolean;
	steps: RelationshipStep[];
	commonAncestors: Person[];
	kinship: Kinship;
};
