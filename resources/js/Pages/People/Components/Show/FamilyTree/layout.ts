import { Sex, type Person, type ShowPersonResource } from '@/types/resources/people';
import toRoman from '@/helpers/toRoman';

type Parent = NonNullable<ShowPersonResource['father']>;
type Child = ShowPersonResource['children'][number];

type Family = {
	key: string;
	partner: Person | null;
	label: string | null;
	children: Child[];
};

type Point = [x: number, y: number];

export type PlacedCard = {
	key: string;
	person: Person | null;
	label: string | null;
	current: boolean;
	left: number;
	top: number;
};

export type Tree = {
	cards: PlacedCard[];
	paths: string[];
	origin: Point;
	width: number;
	height: number;
};

export const cardWidth = 126;

export const slotHeight = 84;

// vertical space between a card and a bar connecting it
const gap = 16;

const spacing = 28;
const groupSpacing = 56;

const radius = 5;

const step = cardWidth + spacing;

// leave room between partners for the line to their children
const coupleStep = cardWidth + groupSpacing;

const rowHeight = slotHeight + (3 * gap);

const grandparents = 0;
const parents = 1;
const generation = 2;
const children = 3;

// center the person at x = 0 and their parents above them.
// children connect at a couple’s midpoint, or at each partner in a row.
export function layoutTree(person: ShowPersonResource): Tree {
	const tree = new TreeBuilder();
	const otherParents = new Map(person.otherParents.map((p) => [p.id, p]));

	const families = groupFamilies(
		'own',
		person.children,
		(child) => (child.fatherId === person.id ? child.motherId : child.fatherId),
		otherParents,
		person.marriages.map((marriage) => ({
			key: `own-marriage-${marriage.id}`,
			partner: marriage.partner,
			label: person.marriages.length > 1 && marriage.order ? `∞ ${toRoman(marriage.order)}` : '∞',
			children: [],
		})),
	);

	// keep siblings opposite partners to avoid crossing their descendants’ connectors
	const side = person.sex === Sex.FEMALE ? -1 : 1;

	// each side lists siblings from the person outwards
	const [leftSiblings, rightSiblings] = families.length === 0
		? [person.siblings.slice(0, person.siblingsBefore).reverse(), person.siblings.slice(person.siblingsBefore)]
		: (side === 1 ? [[...person.siblings].reverse(), []] : [[], person.siblings]);

	tree.place('self', { ...person, visible: true }, generation, 0);

	let reach = 0;

	if (families.length === 1) {
		const [family] = families;
		reach = coupleStep;

		tree.place(family.key, family.partner, generation, side * reach, family.label);
		tree.placeChildren('child', family.children, children, tree.couple(generation, 0, side * reach));
	} else if (families.length > 1) {
		let childrenEdge = -Infinity;

		for (const [i, family] of families.entries()) {
			// keep this descent line beyond the previous family’s children
			reach = Math.max(reach + step, childrenEdge - (cardWidth / 2) + (step / 2));

			tree.place(family.key, family.partner, generation, side * reach, family.label);

			if (!family.children.length) continue;

			// shift earlier families’ children inward to leave room for later partners
			const width = rowWidth(family.children.length);
			const wanted = i < families.length - 1 ? reach - ((width - cardWidth) / 2) : reach;
			const center = Math.max(wanted, childrenEdge + groupSpacing + (width / 2));

			tree.placeChildren('child', family.children, children, [side * reach, slotMiddle(generation)], side * center);

			childrenEdge = center + (width / 2);
		}

		tree.line(generation, 0, side * reach);
	}

	// distances from the person to the outermost cards in their row
	let leftEdge = (side === -1 ? reach : 0) + (cardWidth / 2);
	let rightEdge = (side === 1 ? reach : 0) + (cardWidth / 2);

	const siblingXs = [0];

	for (const sibling of leftSiblings) {
		leftEdge += step;
		siblingXs.push((cardWidth / 2) - leftEdge);
		tree.place(`sibling-${sibling.id}`, sibling, generation, siblingXs.at(-1)!);
	}

	for (const sibling of rightSiblings) {
		rightEdge += step;
		siblingXs.push(rightEdge - (cardWidth / 2));
		tree.place(`sibling-${sibling.id}`, sibling, generation, siblingXs.at(-1)!);
	}

	if (!person.father && !person.mother) {
		return tree.build();
	}

	const distance = (parentReach(person.father) + parentReach(person.mother) + groupSpacing) / 2;

	tree.place('father', person.father, parents, -distance);
	tree.place('mother', person.mother, parents, distance);
	tree.descend(tree.couple(parents, -distance, distance), siblingXs, generation);

	for (const [key, parent, x] of [['father', person.father, -distance], ['mother', person.mother, distance]] as const) {
		if (!parent || !hasParents(parent)) continue;

		tree.place(`${key}-father`, parent.father, grandparents, x - (coupleStep / 2));
		tree.place(`${key}-mother`, parent.mother, grandparents, x + (coupleStep / 2));
		tree.descend(tree.couple(grandparents, x - (coupleStep / 2), x + (coupleStep / 2)), [x], parents);
	}

	const parentFamilies = [
		[-1, groupFamilies('father', person.siblingsFather, (sibling) => sibling.motherId, otherParents), leftEdge],
		[1, groupFamilies('mother', person.siblingsMother, (sibling) => sibling.fatherId, otherParents), rightEdge],
	] as const;

	for (const [parentSide, partnerFamilies, edge] of parentFamilies) {
		let x = distance;
		let childrenEdge = edge;

		for (const family of partnerFamilies) {
			x = Math.max(x + step, childrenEdge + groupSpacing + (rowWidth(family.children.length) / 2));
			childrenEdge = x + (rowWidth(family.children.length) / 2);

			tree.place(family.key, family.partner, parents, parentSide * x);
			tree.placeChildren('sibling', family.children, generation, [parentSide * x, slotMiddle(parents)]);
		}

		if (partnerFamilies.length) {
			tree.line(parents, parentSide * distance, parentSide * x);
		}
	}

	return tree.build();
}

class TreeBuilder {
	private cards: Array<Omit<PlacedCard, 'left' | 'top'> & { x: number, y: number }> = [];
	private paths: string[] = [];

	place(key: string, person: Person | null, row: number, x: number, label: string | null = null) {
		this.cards.push({ key, person, label, current: key === 'self', x, y: row * rowHeight });
	}

	placeChildren(prefix: string, children: Child[], row: number, junction: Point, center = junction[0]) {
		if (!children.length) return;

		const first = center - (rowWidth(children.length) / 2) + (cardWidth / 2);
		const xs = children.map((_, i) => first + (i * step));

		children.forEach((child, i) => this.place(`${prefix}-${child.id}`, child, row, xs[i]));
		this.descend(junction, xs, row);
	}

	line(row: number, from: number, to: number) {
		this.paths.push(`M ${from} ${slotMiddle(row)} H ${to}`);
	}

	couple(row: number, a: number, b: number): Point {
		this.line(row, a, b);

		return [(a + b) / 2, slotMiddle(row)];
	}

	// the junction may be above the children, or to their side
	descend(junction: Point, xs: number[], row: number) {
		const [jx, jy] = junction;
		const bar = (row * rowHeight) - gap;
		const middle = slotMiddle(row);

		const left = Math.min(...xs, jx);
		const right = Math.max(...xs, jx);

		if (left === right) {
			this.paths.push(`M ${jx} ${jy} V ${middle}`);
			return;
		}

		// ends turn toward a child or the junction; keep T-junctions square
		function end(x: number): [Point, number] {
			return xs.includes(x) ? [[x, middle], x === jx ? 0 : radius] : [[x, jy], radius];
		}

		const [start, startRadius] = end(left);
		const [finish, finishRadius] = end(right);

		this.paths.push(rounded([start, [left, bar], [right, bar], finish], [startRadius, finishRadius]));

		for (const x of xs) {
			if (x !== left && x !== right) this.paths.push(`M ${x} ${bar} V ${middle}`);
		}

		if (xs.includes(jx) || (jx > left && jx < right)) {
			this.paths.push(`M ${jx} ${jy} V ${bar}`);
		}
	}

	// normalize card positions; the renderer translates paths by the same origin
	build(): Tree {
		const minLeft = Math.min(...this.cards.map((c) => c.x)) - (cardWidth / 2);
		const minTop = Math.min(...this.cards.map((c) => c.y));
		const maxRight = Math.max(...this.cards.map((c) => c.x)) + (cardWidth / 2);
		const maxBottom = Math.max(...this.cards.map((c) => c.y)) + slotHeight;

		return {
			cards: this.cards.map(({ x, y, ...card }) => ({
				...card,
				left: x - (cardWidth / 2) - minLeft,
				top: y - minTop,
			})),
			paths: this.paths,
			origin: [-minLeft, -minTop],
			width: maxRight - minLeft,
			height: maxBottom - minTop,
		};
	}
}

// preserve the supplied families’ order; append unknown parents last
function groupFamilies(
	prefix: string,
	children: Child[],
	otherParentOf: (child: Child) => number | null,
	otherParents: Map<number, Person>,
	families: Family[] = [],
) {
	let unknown: Family | null = null;

	for (const child of children) {
		const id = otherParentOf(child);
		const partner = id === null ? undefined : otherParents.get(id);
		let family = families.find((f) => f.partner !== null && f.partner.id === id);

		if (!family && partner) {
			family = { key: `${prefix}-partner-${partner.id}`, partner, label: null, children: [] };
			families.push(family);
		}

		if (!family) {
			unknown ??= { key: `${prefix}-unknown`, partner: null, label: null, children: [] };
			family = unknown;
		}

		family.children.push(child);
	}

	return unknown ? [...families, unknown] : families;
}

function hasParents(parent: Parent | null) {
	return parent !== null && (parent.father !== null || parent.mother !== null);
}

// half of the width taken by the parent, with their parents above them
function parentReach(parent: Parent | null) {
	return hasParents(parent) ? (coupleStep + cardWidth) / 2 : cardWidth / 2;
}

function rowWidth(count: number) {
	return (count * step) - spacing;
}

function slotMiddle(row: number) {
	return (row * rowHeight) + (slotHeight / 2);
}

function rounded(points: Point[], radii: number[] = []) {
	let d = `M ${points[0].join(' ')}`;

	for (let i = 1; i < points.length - 1; i++) {
		const previous = points[i - 1];
		const [x, y] = points[i];
		const next = points[i + 1];
		const r = Math.min(radii[i - 1] ?? radius, distance(previous, points[i]) / 2, distance(points[i], next) / 2);

		const [inX, inY] = direction(previous, points[i]);
		const [outX, outY] = direction(points[i], next);

		d += ` L ${x - (inX * r)} ${y - (inY * r)} Q ${x} ${y} ${x + (outX * r)} ${y + (outY * r)}`;
	}

	return `${d} L ${points.at(-1)!.join(' ')}`;
}

function distance(a: Point, b: Point) {
	return Math.hypot(b[0] - a[0], b[1] - a[1]);
}

function direction(a: Point, b: Point): Point {
	const length = distance(a, b) || 1;

	return [(b[0] - a[0]) / length, (b[1] - a[1]) / length];
}
