import type { Person, ShowPersonResource, YearRange } from '@/types/resources/people';

// how many years a bar fades over when its birth or death year is unknown
const unknownFade = 40;

// people born longer ago are not drawn as alive, even when not marked as dead
const maxAge = 110;

export type RowPerson = Pick<Extract<Person, { visible: true }>, 'id' | 'name' | 'familyName' | 'lastName' | 'isDead'>;

/**
 * A bar end fades between `from` and `to`, which are equal for exact years.
 * Years are fractional, so an exact year sits in the middle of its interval.
 */
type Edge = { from: number, to: number };

export type Marker = {
	key: string;
	type: 'marriage' | 'divorce';
	at: number;
	years: YearRange;
};

export type Row = {
	key: string;
	person: RowPerson;
	current: boolean;
	start: Edge;
	end: Edge;
	alive: boolean;
	birth: YearRange | null;
	markers: Marker[];
};

export type Group = {
	key: string;
	label: string | null;
	rows: Row[];
};

export type Timeline = {
	groups: Group[];
	min: number;
	max: number;
};

export function buildTimeline(person: ShowPersonResource, now: Date): Timeline {
	const lifespans = new Map(person.timeline.people.map((l) => [l.id, l]));
	const year = now.getFullYear() + (dayOfYear(now) / 365);

	const markers = new Map<number, Marker[]>();
	function addMarkers(id: number, list: Marker[]) {
		markers.set(id, [...markers.get(id) ?? [], ...list]);
	}

	for (const marriage of person.marriages) {
		const years = person.timeline.marriages.find((m) => m.id === marriage.id);
		if (!years) continue;

		const list: Marker[] = [];
		if (years.start) list.push(marker(`marriage-${marriage.id}`, 'marriage', years.start));
		if (years.divorce) list.push(marker(`divorce-${marriage.id}`, 'divorce', years.divorce));

		addMarkers(person.id, list);
		addMarkers(marriage.partner.id, list);
	}

	function row(group: string, p: RowPerson, current = false): Row | null {
		const lifespan = lifespans.get(p.id);
		if (!lifespan || (!lifespan.birth && !lifespan.death)) return null;

		const { birth, death } = lifespan;
		const alive = !lifespan.dead && !death && birth !== null && birth[0] > year - maxAge;

		let start = birth ? edge(birth) : null;
		let end = death ? edge(death) : alive ? { from: year, to: year } : null;

		// at least one of them is known
		start ??= { from: end!.from - unknownFade, to: end!.from };
		end ??= { from: start.to, to: start.to + unknownFade };

		return {
			key: `${group}-${p.id}`,
			person: p,
			current,
			start,
			end,
			alive,
			birth,
			markers: markers.get(p.id) ?? [],
		};
	}

	function rows(group: string, people: Person[]) {
		return people
			.filter((p) => p.visible)
			.map((p) => row(group, p))
			.filter((r) => r !== null);
	}

	const self = row('siblings', person, true);
	const siblings = rows('siblings', person.siblings);
	if (self) siblings.splice(Math.min(person.siblingsBefore, siblings.length), 0, self);

	const groups: Group[] = [
		{
			key: 'parents',
			label: 'people.parents',
			rows: rows('parents', [person.father, person.mother].filter((p) => p !== null)),
		},
		{
			key: 'siblings',
			label: siblings.length > (self ? 1 : 0) ? 'people.siblings' : null,
			rows: siblings,
		},
		{
			key: 'siblings-father',
			label: 'people.siblings_father',
			rows: rows('siblings-father', person.siblingsFather),
		},
		{
			key: 'siblings-mother',
			label: 'people.siblings_mother',
			rows: rows('siblings-mother', person.siblingsMother),
		},
		{
			key: 'marriages',
			label: 'people.marriages',
			rows: rows('marriages', person.marriages.filter((m) => m.perm.view).map((m) => m.partner)),
		},
		{
			key: 'children',
			label: 'people.children',
			rows: rows('children', person.children),
		},
	].filter((group) => group.rows.length);

	const all = groups.flatMap((group) => group.rows);

	return {
		groups,
		min: Math.min(...all.map((r) => r.start.from), ...all.flatMap((r) => r.markers.map((m) => m.years[0]))),
		max: Math.max(...all.map((r) => r.end.to), ...all.flatMap((r) => r.markers.map((m) => m.years[1] + 1))),
	};
}

/**
 * The age at a year, as a range when the birth year is uncertain,
 * or null outside the part of the bar where the person may have lived.
 */
export function ageAt(row: Row, at: number): string | null {
	if (!row.birth || at < row.birth[0] || at > (row.alive ? row.end.from : row.end.to)) return null;

	const year = Math.floor(at);
	const lo = Math.max(0, year - row.birth[1]);
	const hi = Math.max(0, year - row.birth[0]);

	return lo === hi ? `${lo}` : `${lo}–${hi}`;
}

export function formatYears(years: YearRange) {
	const [from, to] = years;
	return from === to ? `${from}` : `${from}–${to}`;
}

function edge(years: YearRange): Edge {
	const [from, to] = years;
	return from === to ? { from: from + 0.5, to: from + 0.5 } : { from, to: to + 1 };
}

function marker(key: string, type: Marker['type'], years: YearRange): Marker {
	return { key, type, at: (years[0] + years[1] + 1) / 2, years };
}

function dayOfYear(date: Date) {
	return (date.getTime() - new Date(date.getFullYear(), 0, 1).getTime()) / 86_400_000;
}
