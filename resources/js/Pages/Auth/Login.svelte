<script lang="ts">
	import { useForm } from '@inertiajs/svelte';
	import { route } from 'ziggy-js';
	import { authLayoutTitle } from '@/helpers/context';
	import { t } from '@/helpers/translations';
	import * as Form from '@/Components/Forms';
	import Button from '@/Components/Primitives/Button.svelte';
	import Link from '@/Components/Primitives/Link.svelte';

	let { errors }: SharedProps = $props();

	const title = authLayoutTitle.get();
	$title = t('auth.signing_in');

	const form = useForm({ username: '', password: '', remember: false });

	function onsubmit(event: SubmitEvent) {
		event.preventDefault();
		$form.post(route('login'));
	}
</script>

<svelte:head>
	<title>{t('auth.signing_in')} - Arbor</title>
</svelte:head>

<form {onsubmit}>
	<div class="flex flex-wrap">
		<div class="w-full sm:w-1/2 sm:pr-1 pb-2 sm:pb-0">
			<Form.Input
				bind:value={$form.username}
				autofocus
				autocomplete="username"
				placeholder={t('auth.username_or_email').toLowerCase()}
				error={errors.username || errors.password}
			/>
		</div>
		<div class="w-full sm:w-1/2 sm:pl-1">
			<Form.Input
				bind:value={$form.password}
				type="password"
				autocomplete="current-password"
				placeholder={t('auth.password').toLowerCase()}
				error={errors.username || errors.password}
			/>
		</div>
	</div>

	<Form.Error error={errors.username}/>
	<Form.Error error={errors.password}/>

	<div class="mt-4 flex flex-wrap items-center justify-between gap-2">
		<div class="flex grow items-center" style:flex-grow="10">
			<input type="checkbox" bind:checked={$form.remember} id="remember" class="form-checkbox size-3.5">
			<label for="remember" class="ml-1"><small>{t('auth.remember')}</small></label>
		</div>

		<div class="flex grow items-center justify-between gap-2">
			<Link href={route('password.request')}>
				<small>{t('auth.forgot_password')}</small>
			</Link>
			<Button type="submit">
				{t('auth.sign_in')}
			</Button>
		</div>
	</div>
</form>
