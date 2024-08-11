/* eslint-disable no-var */

import type { Language } from '@/helpers/translations';

export {};

declare global {
	var globalProps: {
		currentLocale: Language;
		fallbackLocale: Language;
	};
}
