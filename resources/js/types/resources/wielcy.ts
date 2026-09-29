export type Wielcy = {
	name: string | null;
	middleName: string | null;
	surname: string | null;
	father: WielcyRelative | null;
	mother: WielcyRelative | null;
};

export type WielcyRelative = {
	name: string | null;
	surname: string | null;
	id: string | null;
	url: string | null;
	arborId: number | null;
	canBeViewedInArbor: boolean;
};
