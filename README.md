# Sabha - Business Community Platform

Welcome to **Sabha**, a premium business community platform built with a modern stack!

## Project Structure

This is a single Laravel application in `/backend`:

-   **Public site & admin panel**: Laravel 11 + Blade + Livewire + Alpine.js + Tailwind CSS.
-   **JSON API**: `routes/api.php` (Laravel Sanctum), kept for backward compatibility.

## Prerequisites

-   **Docker Desktop**: Required to run the environment via Laravel Sail.
-   **PHP 8.2+**: For local development.
-   **Node.js 18+**: For building frontend assets (Vite).

## Setup Instructions

1.  Navigate into the backend directory:
    ```bash
    cd backend
    ```
2.  Install PHP dependencies:
    ```bash
    composer install
    ```
3.  Copy the environment file:
    ```bash
    cp .env.example .env
    ```
4.  Generate an application key:
    ```bash
    php artisan key:generate
    ```
5.  Install Node dependencies and build frontend assets:
    ```bash
    npm install
    npm run build
    ```
6.  Start the environment with Docker (Sail):
    ```bash
    ./vendor/bin/sail up -d
    ```
7.  Run migrations:
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

## Development

-   App (site + admin): [http://localhost:8000](http://localhost:8000)
-   Run `npm run dev` inside `backend/` for hot-reloading Vite assets while developing.

## License

This project is licensed under the [MIT license](https://opensource.org/licenses/MIT).
