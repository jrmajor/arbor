import type { Language } from '@/helpers/translations';

export function localeToSwitchTo(current: Language): Language {
	return current === 'en' ? 'pl' : 'en';
}

export function localeName(locale: Language): string {
	const displayNames = new Intl.DisplayNames([locale], { type: 'language' });
	const name = displayNames.of(locale) ?? locale;
	return name.charAt(0).toUpperCase() + name.slice(1);
}
