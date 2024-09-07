/* eslint-disable import/order */

import { FluentBundle, FluentResource } from '@fluent/bundle';
import { type Language } from './translations';

import enMisc from '../../../lang/en/misc.ftl?raw';
import plMisc from '../../../lang/pl/misc.ftl?raw';
import deMisc from '../../../lang/de/misc.ftl?raw';
import enAuth from '../../../lang/en/auth.ftl?raw';
import plAuth from '../../../lang/pl/auth.ftl?raw';
import deAuth from '../../../lang/de/auth.ftl?raw';
import enSettings from '../../../lang/en/settings.ftl?raw';
import plSettings from '../../../lang/pl/settings.ftl?raw';
import enPasswords from '../../../lang/en/passwords.ftl?raw';
import plPasswords from '../../../lang/pl/passwords.ftl?raw';
import enPeople from '../../../lang/en/people.ftl?raw';
import plPeople from '../../../lang/pl/people.ftl?raw';
import dePeople from '../../../lang/de/people.ftl?raw';
import enMarriages from '../../../lang/en/marriages.ftl?raw';
import plMarriages from '../../../lang/pl/marriages.ftl?raw';
import deMarriages from '../../../lang/de/marriages.ftl?raw';
import enActivities from '../../../lang/en/activities.ftl?raw';
import plActivities from '../../../lang/pl/activities.ftl?raw';
import enUsers from '../../../lang/en/users.ftl?raw';
import plUsers from '../../../lang/pl/users.ftl?raw';

export const bundles: Record<Language, Record<string, FluentBundle>> = {
	en: {
		misc: createBundle('en', enMisc),
		auth: createBundle('en', enAuth),
		settings: createBundle('en', enSettings),
		passwords: createBundle('en', enPasswords),
		people: createBundle('en', enPeople),
		marriages: createBundle('en', enMarriages),
		activities: createBundle('en', enActivities),
		users: createBundle('en', enUsers),
	},
	pl: {
		misc: createBundle('pl', plMisc),
		auth: createBundle('pl', plAuth),
		settings: createBundle('pl', plSettings),
		passwords: createBundle('pl', plPasswords),
		people: createBundle('pl', plPeople),
		marriages: createBundle('pl', plMarriages),
		activities: createBundle('pl', plActivities),
		users: createBundle('pl', plUsers),
	},
	de: {
		misc: createBundle('de', deMisc),
		auth: createBundle('de', deAuth),
		people: createBundle('de', dePeople),
		marriages: createBundle('de', deMarriages),
	},
};

function createBundle(locale: string, data: string) {
	const bundle = new FluentBundle(locale);
	bundle.addResource(new FluentResource(data));
	return bundle;
}
