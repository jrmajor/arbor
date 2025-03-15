import base, { svelte } from '@jrmajor/eslint-config';
import globals from 'globals';

/** @type {import('eslint').Linter.Config[]} */
export default [
	...base,
	...svelte,
	{
		languageOptions: {
			globals: {
				...globals.browser,
				...globals.node,
				SharedProps: 'readonly',
				SharedUser: 'readonly',
				FlashMessage: 'readonly',
				PaginatedResource: 'readonly',
				PaginationLinks: 'readonly',
				PaginationMeta: 'readonly',
				PaginationLink: 'readonly',
			},
		},
		rules: {
			// todo: enable
			'svelte/require-each-key': 'off',
		},
	},
	{
		ignores: [
			'bootstrap/ssr',
			'public',
			'resources/js/types/ziggy.*',
		],
	},
];
