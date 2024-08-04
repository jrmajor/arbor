/* eslint-disable no-console */
import { FluentBundle, FluentResource, type FluentVariable } from '@fluent/bundle';
import type { Pattern } from '@fluent/bundle/esm/ast';

import enMisc from '../../../lang/en/misc.ftl?raw';
import plMisc from '../../../lang/pl/misc.ftl?raw';
import deMisc from '../../../lang/de/misc.ftl?raw';
import enAuth from '../../../lang/en/auth.ftl?raw';
import plAuth from '../../../lang/pl/auth.ftl?raw';
import deAuth from '../../../lang/de/auth.ftl?raw';
import enPeople from '../../../lang/en/people.ftl?raw';
import plPeople from '../../../lang/pl/people.ftl?raw';
import dePeople from '../../../lang/de/people.ftl?raw';
import enMarriages from '../../../lang/en/marriages.ftl?raw';
import plMarriages from '../../../lang/pl/marriages.ftl?raw';
import deMarriages from '../../../lang/de/marriages.ftl?raw';

export type Language = 'en' | 'pl' | 'de';

export function t(key: string, args: Record<string, FluentVariable> = {}) {
	const language = window.userLanguage;
	const [bundleName, messageName, attrName] = key.split('.');
	const bundle = bundles[language][bundleName];
	if (!bundle) {
		console.error(`Bundle ${bundleName} not found for ${language} language.`);
		return key;
	}
	const message = bundle.getMessage(messageName);
	if (!message) {
		console.error(`Message ${bundleName}.${messageName} not found in ${language} bundle.`);
		return key;
	}
	let pattern: Pattern;
	if (attrName) {
		const attribute = message.attributes[attrName];
		if (!attribute) {
			console.error(`Attribute ${bundleName}.${messageName}.${attrName} not found in ${language} bundle.`);
			return key;
		}
		pattern = attribute;
	} else {
		if (!message.value) {
			console.error(`Message ${bundleName}.${messageName} in ${language} bundle has no value.`);
			return key;
		}
		pattern = message.value;
	}

	const errors: Error[] = [];
	const formatted = bundle.formatPattern(pattern, args, errors);
	errors.forEach((error) => console.error(error));
	return formatted;
}

const bundles: Record<Language, Record<string, FluentBundle>> = {
	en: {
		misc: createBundle('en', enMisc),
		auth: createBundle('en', enAuth),
		people: createBundle('en', enPeople),
		marriages: createBundle('en', enMarriages),
	},
	pl: {
		misc: createBundle('pl', plMisc),
		auth: createBundle('pl', plAuth),
		people: createBundle('pl', plPeople),
		marriages: createBundle('pl', plMarriages),
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
