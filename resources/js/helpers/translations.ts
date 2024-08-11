/* eslint-disable no-console */
import { FluentBundle, type FluentVariable } from '@fluent/bundle';
import type { Pattern } from '@fluent/bundle/esm/ast';
import { bundles } from './translationBundles';
import { getLocale } from './translationsState.svelte';

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
	const { currentLocale, fallbackLocale } = getLocale();

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
	if (!bundle) return null;

	const message = bundle.getMessage(messageName);
	if (!message) return null;

	if (!attrName) {
		if (!message.value) return null;
		return [bundle, message.value];
	}

	if (!message.attributes[attrName]) return null;
	return [bundle, message.attributes[attrName]];
}
