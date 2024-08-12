## About

Arbor is a genealogy application with Laravel backend in PHP,
and frontend built in Svelte with Inertia.js and Tailwind CSS.

![Screenshot of person view](resources/arbor.png)

## Installation

Clone this repository and install it like you normally install Laravel application.

- Install dependencies (`composer install && yarn install`)
- Copy `.env.example` to `.env` and set environment variables
- Set application key with `php artisan key:generate`
- Run database migrations (`php artisan migrate`)
- Start Vite dev server with `yarn dev`

## Testing

This application uses PHPUnit for testing and PHPStan for static analysis.

```sh
php artisan test --parallel  # Tests
vendor/bin/phpstan analyze   # Static analysis
vendor/bin/php-cs-fixer fix  # Formatting
```
