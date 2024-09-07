import { extname } from 'path';
import { parse, Visitor, type Annotation } from '@fluent/syntax';
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

			// eslint-disable-next-line @typescript-eslint/no-this-alias
			const parentThis = this;
			class Validator extends Visitor {
				visitAnnotation(node: Annotation): void {
					parentThis.warn(`[${node.code}] ${node.message}`, node.span?.start);
				}
			}

			(new Validator()).visit(parse(code, {}));

			return `
				import { FluentBundle, FluentResource } from '@fluent/bundle';

				const bundle = new FluentBundle(${JSON.stringify(locale)});
				bundle.addResource(new FluentResource(${JSON.stringify(code)}));
				export default bundle;
				`;
		},
	} as Plugin;
}
