<script lang="ts">
	import { route } from 'ziggy-js';
	import { useForm } from '@inertiajs/svelte';
	import { t } from '@/helpers/translations';
	import { authLayoutTitle } from '@/helpers/context';
	import Link from '@/Components/Primitives/Link.svelte';
	import Button from '@/Components/Primitives/Button.svelte';

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
			<input
				type="text"
				bind:value={$form.email}
				autocomplete="email"
				placeholder={t('passwords.email')}
				class="form-input w-full"
				class:invalid={errors.email}
			>
		</div>
	</div>
	{#if errors.email}
		<div class="w-full leading-none mt-1 text-left">
			<small class="text-red-500">{errors.email}</small>
		</div>
	{/if}

	<div class="mt-4 flex items-center justify-between gap-2">
		<Link href={route('login')}>
			<small>{t('auth.sign_in')}</small>
		</Link>
		<Button type="submit">
			{t('passwords.send_link')}
		</Button>
	</div>
</form>
