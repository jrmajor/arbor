import type { FluentVariable } from '@fluent/bundle';
import type { Kinship, Person } from '@/types/resources/people';
import { t } from '@/helpers/translations';

export function kinshipName(kinship: Kinship) {
	const { type, ...details } = kinship;
	const greats = typeof details.greats === 'number' ? details.greats : 0;

	// Polish words siblings of ancestors and grandchildren of siblings
	// from the grandparent or grandchild, so they need one "pra" less
	const pra = type === 'grandparent' || type === 'grandchild' ? greats : Math.max(0, greats - 1);

	const args: Record<string, FluentVariable> = {
		great: 'great-'.repeat(greats),
		pra: 'pra'.repeat(pra),
	};

	for (const [key, value] of Object.entries(details)) {
		args[key] = typeof value === 'boolean' ? String(value) : value;
	}

	return t(`relationship.kind.${type}`, args);
}

export function shortName(person: Person) {
	return person.visible ? `${person.name} ${person.lastName ?? person.familyName}` : `[${t('misc.hidden')}]`;
}
