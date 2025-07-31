import base, { css, js, svelte } from '@jrmajor/eslint-config';
import { defineConfig, globalIgnores } from 'eslint/config';
import globals from 'globals';

export default defineConfig([
	base,
	js,
	svelte,
	css,
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
	globalIgnores([
		'bootstrap/ssr',
		'public/build',
		'resources/js/types/ziggy.*',
		// todo: @eslint/css errors
		'resources/css/tailwind.css',
		'resources/css/forms.css',
	]),
]);
