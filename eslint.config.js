import js from '@eslint/js';
import ts from 'typescript-eslint';
import svelte from 'eslint-plugin-svelte';
import stylistic from '@stylistic/eslint-plugin';
import globals from 'globals';

/** @type {import('eslint').Linter.Config[]} */
export default [
	js.configs.recommended,
	...ts.configs.recommended,
	...svelte.configs['flat/recommended'],
	{
		plugins: {
			'@stylistic': stylistic,
		},
		languageOptions: {
			globals: {
				...globals.browser,
				...globals.node,
				SharedProps: 'readonly',
				SharedUser: 'readonly',
				FlashData: 'readonly',
				PaginatedResource: 'readonly',
				PaginationLinks: 'readonly',
				PaginationMeta: 'readonly',
				PaginationLink: 'readonly',
			},
		},
		rules: {
			// seems broken, causes parse errors
			'@typescript-eslint/no-unused-expressions': 'off',
			// js
			'arrow-body-style': 'error',
			'block-scoped-var': 'error',
			'default-case-last': 'error',
			'default-param-last': 'error',
			'dot-notation': 'error',
			'func-names': ['error', 'as-needed'],
			'func-style': ['error', 'declaration'],
			'grouped-accessor-pairs': 'error',
			'no-await-in-loop': 'error',
			'no-console': 'error',
			'no-constructor-return': 'error',
			'no-duplicate-imports': 'error',
			'no-else-return': 'error',
			'no-implicit-coercion': ['error', { boolean: false }],
			'no-implied-eval': 'error',
			'no-lone-blocks': 'error',
			'no-lonely-if': 'error',
			'no-multi-str': 'error',
			'no-negated-condition': 'error',
			'no-nested-ternary': 'error',
			'no-throw-literal': 'error',
			'no-undefined': 'error',
			'no-underscore-dangle': 'error',
			'no-unneeded-ternary': 'error',
			'no-useless-concat': 'error',
			'no-useless-rename': 'error',
			'no-useless-return': 'error',
			'no-var': 'error',
			'object-shorthand': 'error',
			'operator-assignment': 'error',
			'prefer-arrow-callback': 'error',
			'prefer-destructuring': ['error', {
				VariableDeclarator: { array: true, object: true },
				AssignmentExpression: { array: false, object: false },
			}],
			'prefer-exponentiation-operator': 'error',
			'prefer-template': 'error',
			curly: ['error', 'multi-line'],
			eqeqeq: 'error',
			yoda: 'error',
			// svelte
			'svelte/block-lang': ['error', { script: 'ts', style: 'postcss' }],
			'svelte/no-at-html-tags': 'off',
			'svelte/no-dupe-use-directives': 'error',
			'svelte/no-ignored-unsubscribe': 'error',
			'svelte/no-useless-mustaches': 'error',
			'svelte/require-optimized-style-attribute': 'error',
			'svelte/valid-each-key': 'error',
			'svelte/valid-prop-names-in-kit-pages': 'error',
			// stylistic
			'@stylistic/array-bracket-newline': ['error', 'consistent'],
			'@stylistic/array-bracket-spacing': 'error',
			'@stylistic/array-element-newline': ['error', 'consistent'],
			'@stylistic/arrow-parens': 'error',
			'@stylistic/arrow-spacing': 'error',
			'@stylistic/block-spacing': 'error',
			'@stylistic/brace-style': 'error',
			'@stylistic/comma-dangle': ['error', 'always-multiline'],
			'@stylistic/comma-spacing': 'error',
			'@stylistic/comma-style': 'error',
			'@stylistic/computed-property-spacing': 'error',
			'@stylistic/dot-location': ['error', 'property'],
			'@stylistic/eol-last': 'error',
			'@stylistic/func-call-spacing': 'error',
			'@stylistic/function-call-argument-newline': ['error', 'consistent'],
			'@stylistic/function-call-spacing': 'error',
			'@stylistic/function-paren-newline': ['error', 'consistent'],
			'@stylistic/generator-star-spacing': 'error',
			'@stylistic/implicit-arrow-linebreak': 'error',
			'@stylistic/indent': ['error', 'tab'],
			'@stylistic/indent-binary-ops': ['error', 'tab'],
			'@stylistic/key-spacing': 'error',
			'@stylistic/keyword-spacing': 'error',
			'@stylistic/linebreak-style': 'error',
			'@stylistic/lines-around-comment': 'error',
			'@stylistic/lines-between-class-members': 'error',
			'@stylistic/max-statements-per-line': 'error',
			'@stylistic/member-delimiter-style': 'error',
			'@stylistic/multiline-comment-style': ['error', 'separate-lines'],
			'@stylistic/multiline-ternary': ['error', 'always-multiline'],
			'@stylistic/new-parens': 'error',
			'@stylistic/no-extra-semi': 'error',
			'@stylistic/no-floating-decimal': 'error',
			'@stylistic/no-mixed-operators': 'error',
			'@stylistic/no-mixed-spaces-and-tabs': 'error',
			'@stylistic/no-multi-spaces': 'error',
			'@stylistic/no-multiple-empty-lines': 'error',
			'@stylistic/no-trailing-spaces': 'error',
			'@stylistic/no-whitespace-before-property': 'error',
			'@stylistic/nonblock-statement-body-position': 'error',
			'@stylistic/object-curly-newline': ['error', { consistent: true }],
			'@stylistic/object-curly-spacing': ['error', 'always'],
			'@stylistic/object-property-newline': ['error', { allowAllPropertiesOnSameLine: true }],
			'@stylistic/operator-linebreak': ['error', 'before'],
			'@stylistic/padded-blocks': ['error', 'never'],
			'@stylistic/quote-props': ['error', 'as-needed'],
			'@stylistic/quotes': ['error', 'single'],
			'@stylistic/rest-spread-spacing': 'error',
			'@stylistic/semi': 'error',
			'@stylistic/semi-spacing': 'error',
			'@stylistic/semi-style': 'error',
			'@stylistic/space-before-blocks': 'error',
			'@stylistic/space-before-function-paren': ['error', { named: 'never' }],
			'@stylistic/space-in-parens': 'error',
			'@stylistic/space-infix-ops': 'error',
			'@stylistic/space-unary-ops': 'error',
			'@stylistic/spaced-comment': 'error',
			'@stylistic/switch-colon-spacing': 'error',
			'@stylistic/template-curly-spacing': 'error',
			'@stylistic/template-tag-spacing': 'error',
			'@stylistic/type-annotation-spacing': 'error',
			'@stylistic/type-generic-spacing': 'error',
			'@stylistic/type-named-tuple-spacing': 'error',
			'@stylistic/yield-star-spacing': 'error',
			// ts stylistic
			'@typescript-eslint/array-type': ['error', { default: 'array-simple', readonly: 'array-simple' }],
			'@typescript-eslint/consistent-generic-constructors': 'error',
			'@typescript-eslint/consistent-indexed-object-style': 'error',
			'@typescript-eslint/consistent-type-assertions': 'error',
			'@typescript-eslint/prefer-for-of': 'error',
			'@typescript-eslint/prefer-function-type': 'error',
			// svelte stylistic
			'svelte/derived-has-same-inputs-outputs': 'error',
			'svelte/first-attribute-linebreak': 'error',
			'svelte/html-closing-bracket-spacing': ['error', { startTag: 'never', endTag: 'never', selfClosingTag: 'never' }],
			'svelte/html-quotes': 'error',
			// todo: https://github.com/sveltejs/eslint-plugin-svelte/issues/837
			// 'svelte/html-self-closing': ['error', { void: 'never', normal: 'never' }],
			'svelte/max-attributes-per-line': ['error', { singleline: 8 }],
			'svelte/mustache-spacing': 'off', // false positives
			'svelte/no-spaces-around-equal-signs-in-attribute': 'error',
			'svelte/prefer-class-directive': 'error',
			'svelte/prefer-style-directive': 'error',
			'svelte/shorthand-attribute': 'off', // false positives
			'svelte/shorthand-directive': 'error',
			'svelte/sort-attributes': ['error', { order: [
				'this',
				'bind:this',
				'/^use:/u',
				{ match: ['!/^(?:this|bind:this|use:)$/u'], sort: 'ignore' },
				'/^transition:/u',
				'/^in:/u',
				'/^out:/u',
				'/^animate:/u',
				'class',
				'/^class:/u',
				'style',
				'/^style:/u',
				'/^--/u', // --style-props
			] }],
			'svelte/spaced-html-comment': 'error',
		},
	},
	{
		files: ['**/*.svelte'],
		languageOptions: {
			parserOptions: {
				parser: ts.parser,
				svelteFeatures: {
					experimentalGenerics: true,
				},
			},
		},
		rules: {
			'@stylistic/indent': 'off',
			'@stylistic/indent-binary-ops': 'off',
			'svelte/indent': ['error', { indent: 'tab', alignAttributesVertically: true }],
			'@stylistic/no-trailing-spaces': 'off',
			'svelte/no-trailing-spaces': 'error',
		},
	},
	{
		ignores: [
			'public',
			'resources/js/classic',
			'resources/js/helpers/ziggy.*',
			'vendor',
			'vite.config.[jt]s.timestamp-*',
		],
	},
];
