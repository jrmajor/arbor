export type Pytlewski = {
	name: string;
	middleName: string | null;
	familyName: string;
	lastName: string | null;
	father: PytlewskiRelative | null;
	mother: PytlewskiRelative | null;
	marriages: PytlewskiMarriage[];
	children: PytlewskiRelative[];
	siblings: PytlewskiRelative[];
};

export type PytlewskiMarriage = {
	name: string | null;
	id: number | null;
	url: string | null;
	arborId: number | null;
	canBeViewedInArbor: boolean;
};

export type PytlewskiRelative = {
	name: string | null;
	surname: string | null;
	id: number | null;
	url: string | null;
	arborId: number | null;
	canBeViewedInArbor: boolean;
};
