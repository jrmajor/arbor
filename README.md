## About

Arbor is genealogy application built in PHP 8 with Laravel framework. For frontend it uses Tailwind CSS and Alpine.js.

## Installation

### Requirements

- PHP 8.0
- MySQL 8

### Installation

Clone this repository and install it like you normally install Laravel application.

- Install dependencies (`composer install && yarn install`)
- Generate assets with `yarn dev`
- Copy `.env.example` to `.env` and set environment variables
- Set application key with `php artisan key:generate`
- Run database migrations (`php artisan migrate`)

## Testing

This application uses [Pest](https://pestphp.com) for testing. MySQL 8 is required. You may set test database name in `phpunit.xml` (defaults to `arbor_tests`).

```sh-session
$ vendor/bin/pest
```
