import { createInertiaApp } from '@inertiajs/svelte';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { mount } from 'svelte';

createInertiaApp({
    title: (title) => `${title} - Arbor`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.svelte`, import.meta.glob('./Pages/**/*.svelte')),
    setup({ el, App, props }) {
        mount(App, { target: el, props })
    },
    progress: {
        color: '#4299e1',
    },
});
