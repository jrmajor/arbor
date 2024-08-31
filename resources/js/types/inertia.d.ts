declare module '@inertiajs/svelte' {
	import type { Component } from 'svelte';
	import type { Action } from 'svelte/action';
	import type { Readable, Writable } from 'svelte/store';
	import type { FormDataConvertible, Method, Page, Progress, Router, VisitOptions } from '@inertiajs/core';

	export const router: Router;
	export const Link: Component;
	export function createInertiaApp(options: {
		id?: string;
		resolve(name: string): Promise<
			| Component
			| {
				default: Component<any, any, any>;
				layout: Array<Component<any, any, any>> | Component<any, any, any> | undefined;
			}
		>;
		setup(props: { el: Element; App: Component; props: Record<string, unknown> }): void;
		title?(title: string): string;
		progress?:
			| false
			| {
				delay?: number;
				color?: string;
				includeCSS?: boolean;
				showSpinner?: boolean;
			};
		page?: Page;
	}): Promise<{ head: string[]; body: string }> | void;
	export const inertia: Action<
		HTMLAnchorElement | HTMLButtonElement,
		(VisitOptions & { href?: string }) | undefined
	>;
	export const page: Readable<Page>;
	export function remember<T>(initialState: T, key: string): Writable<T>;
	export function useForm<TForm extends FormDataType>(data: TForm | (() => TForm)): InertiaForm<TForm>;
	export function useForm<TForm extends FormDataType>(
		rememberKey: string,
		data: TForm | (() => TForm),
	): InertiaForm<TForm>;
	export function useForm<TForm extends FormDataType>(
		rememberKeyOrData: string | TForm | (() => TForm),
		maybeData?: TForm | (() => TForm),
	): InertiaForm<TForm>;

	type FormDataType = object;

	interface InertiaFormProps<TForm extends FormDataType> {
		isDirty: boolean;
		errors: Partial<Record<keyof TForm, string>>;
		hasErrors: boolean;
		processing: boolean;
		progress: Progress | null;
		wasSuccessful: boolean;
		recentlySuccessful: boolean;
		data(): TForm;
		transform(callback: (data: TForm) => object): this;
		defaults(): this;
		defaults(field: keyof TForm, value: FormDataConvertible): this;
		defaults(fields: Partial<TForm>): this;
		reset(...fields: Array<keyof TForm>): this;
		clearErrors(...fields: Array<keyof TForm>): this;
		setError(field: keyof TForm, value: string): this;
		setError(errors: Record<keyof TForm, string>): this;
		submit(method: Method, url: string, options?: Partial<VisitOptions>): void;
		get(url: string, options?: Partial<VisitOptions>): void;
		post(url: string, options?: Partial<VisitOptions>): void;
		put(url: string, options?: Partial<VisitOptions>): void;
		patch(url: string, options?: Partial<VisitOptions>): void;
		delete(url: string, options?: Partial<VisitOptions>): void;
		cancel(): void;
	}

	export type InertiaForm<TForm extends FormDataType> = Writable<TForm & InertiaFormProps<TForm>>;
}
