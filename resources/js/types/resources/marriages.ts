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

export type EditMarriageResource = {
	id: number;
	woman: { id: number | null };
	man: { id: number | null };
	womanOrder: number | null;
	manOrder: number | null;
	rite: Rite | null;
	firstEventType: EventType | null;
	firstEventDateFrom: string | null;
	firstEventDateTo: string | null;
	firstEventPlace: string | null;
	secondEventType: EventType | null;
	secondEventDateFrom: string | null;
	secondEventDateTo: string | null;
	secondEventPlace: string | null;
	divorced: boolean;
	divorceDateFrom: string | null;
	divorceDateTo: string | null;
	divorcePlace: string | null;
};

export enum Rite {
	CIVIL = 'civil',
	ROMANCATHOLIC = 'roman_catholic',
}

export enum EventType {
	MARRIAGE = 'marriage',
	CHURCHMARRIAGE = 'church_marriage',
	CIVILMARRIAGE = 'civil_marriage',
	CONCORDATMARRIAGE = 'concordat_marriage',
}

export const RITES: Rite[] = [
	Rite.CIVIL,
	Rite.ROMANCATHOLIC,
];

export const EVENT_TYPES: EventType[] = [
	EventType.MARRIAGE,
	EventType.CHURCHMARRIAGE,
	EventType.CIVILMARRIAGE,
	EventType.CONCORDATMARRIAGE,
];
