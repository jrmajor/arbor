import type { FluentBundle } from '@fluent/bundle';
import type { Language } from './translations';

const bundles: Record<Language, Record<string, FluentBundle>>
		= { en: {}, pl: {}, de: {} };

const allBundles = import.meta.glob<FluentBundle>(
	'../../../lang/*/*.ftl',
	{ eager: true, import: 'default' },
);

Object.entries(allBundles).forEach(([path, bundle]) => {
	const [, locale, bundleName] = /\/lang\/([a-z]{2})\/([a-z]+).ftl/.exec(path)!;
	bundles[locale as Language][bundleName] = bundle;
});

export { bundles };
