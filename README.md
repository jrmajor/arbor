## About

Arbor is a genealogy application with Laravel backend in PHP,
and frontend built in Svelte with Inertia.js and Tailwind CSS.

![Screenshot of person view](resources/arbor.png)

## Installation

Clone this repository and install it like you normally install Laravel application.

- Install dependencies (`composer install && pnpm install`)
- Create `.env` using `composer setup` script
- Run database migrations (`php artisan migrate`)
- Start dev server with `composer dev`

## Testing

Test your changes using `composer test`, `composer check` and `composer format` scripts.
