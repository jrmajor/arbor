declare module '*.ftl' {
	import { FluentBundle } from '@fluent/bundle';

	const bundle: FluentBundle;
	export default bundle;
}
