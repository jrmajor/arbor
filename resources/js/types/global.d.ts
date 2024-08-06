/* eslint-disable no-var */

import type { Language } from '@/helpers/translations';

export {};

declare global {
	var arborProps: {
		appName: string;
		currentLocale: Language;
		fallbackLocale: Language;
		otherAvailableLocales: Language[];
	};
}
