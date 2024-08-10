/* eslint-disable no-console */
import { FluentBundle, FluentResource, type FluentVariable } from '@fluent/bundle';
import type { Pattern } from '@fluent/bundle/esm/ast';

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

export type Language = 'en' | 'pl' | 'de';

export function t(key: string, args: Record<string, FluentVariable> = {}) {
	const patternAndBundle = getPatternOrFallback(key);
	if (!patternAndBundle) return key;
	const [bundle, pattern] = patternAndBundle;

	const errors: Error[] = [];
	const formatted = bundle.formatPattern(pattern, args, errors);
	errors.forEach((error) => console.error(error));
	return formatted;
}

function getPatternOrFallback(key: string) {
	const { currentLocale, fallbackLocale } = globalThis.arborProps;

	const [bundleName, messageName, attrName] = key.split('.');

	let pattern = getPattern(currentLocale, bundleName, messageName, attrName);
	if (!pattern && currentLocale !== fallbackLocale) {
		pattern = getPattern(fallbackLocale, bundleName, messageName, attrName);
		if (pattern) {
			console.error(`Message ${key} not found for ${currentLocale}.`);
		} else {
			console.error(`Message ${key} not found for ${currentLocale} or ${fallbackLocale}.`);
		}
	} else if (!pattern) {
		console.error(`Message ${key} not found for ${currentLocale}.`);
	}

	return pattern;
}

function getPattern(
	locale: Language,
	bundleName: string,
	messageName: string,
	attrName?: string,
): [FluentBundle, Pattern] | null {
	const bundle = bundles[locale][bundleName];
	if (!bundle) {
		return null;
	}

	const message = bundle.getMessage(messageName);
	if (!message) {
		return null;
	}

	if (!attrName) {
		if (!message.value) return null;
		return [bundle, message.value];
	}

	if (!message.attributes[attrName]) return null;
	return [bundle, message.attributes[attrName]];
}

const bundles: Record<Language, Record<string, FluentBundle>> = {
	en: {
		misc: createBundle('en', enMisc),
		auth: createBundle('en', enAuth),
		settings: createBundle('en', enSettings),
		passwords: createBundle('en', enPasswords),
		people: createBundle('en', enPeople),
		marriages: createBundle('en', enMarriages),
	},
	pl: {
		misc: createBundle('pl', plMisc),
		auth: createBundle('pl', plAuth),
		settings: createBundle('pl', plSettings),
		passwords: createBundle('pl', plPasswords),
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
