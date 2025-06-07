import base, { svelte } from '@jrmajor/eslint-config';
import { defineConfig, globalIgnores } from 'eslint/config';
import globals from 'globals';

export default defineConfig([
	base,
	svelte,
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
			'import-x/no-duplicates': 'off',
			// todo: enable
			'svelte/require-each-key': 'off',
		},
	},
	globalIgnores([
		'bootstrap/ssr',
		'public',
		'resources/js/types/ziggy.*',
	]),
]);
