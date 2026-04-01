declare module '*.ftl' {
	import { FluentBundle } from '@fluent/bundle';

	const bundle: FluentBundle;
	export default bundle;
}

declare global {
	var Ziggy: any;
}

export { };
