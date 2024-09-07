import { extname } from 'path';
import type { Plugin } from 'vite';

interface PluginOptions {
	resolveLocale(path: string): string;
}

export default function fluent(options: PluginOptions) {
	return {
		name: 'fluent',
		async transform(code, id) {
			if (extname(id) !== '.ftl') return null;

			const locale = options.resolveLocale(id);

			return `
				import { FluentBundle, FluentResource } from '@fluent/bundle';

				const bundle = new FluentBundle(${JSON.stringify(locale)});
				bundle.addResource(new FluentResource(${JSON.stringify(code)}));
				export default bundle;
				`;
		},
	} as Plugin;
}
