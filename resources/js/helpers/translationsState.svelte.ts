import { type Language } from './translations';

let currentLocale: Language = $state()!;
let fallbackLocale: Language = $state()!;

export function setLocale(current: Language, fallback: Language) {
	currentLocale = current;
	fallbackLocale = fallback;
}

export function getLocale() {
	return { currentLocale, fallbackLocale };
}
