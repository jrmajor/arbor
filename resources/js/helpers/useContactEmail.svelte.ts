export function useContactEmail() {
	// eslint-disable-next-line svelte/prefer-writable-derived
	let email = $state('');

	$effect(() => {
		// setting it in effect should prevent it from being rendered on server
		email = atob('anJoLm1qckBnbWFpbC5jb20=');
	});

	return {
		get value() {
			return email;
		},
		get href() {
			return `mailto:${this.value}`;
		},
	};
}
