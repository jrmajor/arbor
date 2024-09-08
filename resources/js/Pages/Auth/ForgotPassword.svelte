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
	$title = t('passwords.password_reset');

	const form = useForm({ email: '' });

	function onsubmit(event: SubmitEvent) {
		event.preventDefault();
		$form.post(route('password.email'));
	}
</script>

<svelte:head>
	<title>{t('passwords.password_reset')} - Arbor</title>
</svelte:head>

<form {onsubmit}>
	<div class="flex flex-wrap">
		<div class="w-full">
			<Form.Input
				bind:value={$form.email}
				autocomplete="email"
				placeholder={t('passwords.email').toLowerCase()}
				error={errors.email}
			/>
		</div>
	</div>
	<Form.Error error={errors.email}/>

	<div class="mt-4 flex items-center justify-between gap-2">
		<Link href={route('login')}>
			<small>{t('auth.sign_in')}</small>
		</Link>
		<Button type="submit">
			{t('passwords.send_link')}
		</Button>
	</div>
</form>
