# Sabha - Business Community Platform

Welcome to **Sabha**, a premium business community platform built with a modern stack!

## Project Structure

This project is organized as a monorepo containing both the frontend and backend.

-   **`/frontend`**: Next.js 16 (React 19) application for the user interface.
-   **`/backend`**: Laravel 11 application for the API and business logic.

## Prerequisites

-   **Docker Desktop**: Required to run the environment via Laravel Sail.
-   **PHP 8.2+**: For local backend development.
-   **Node.js 18+**: For frontend development.

## Setup Instructions

### Backend (Laravel)

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
5.  Start the environment with Docker (Sail):
    ```bash
    ./vendor/bin/sail up -d
    ```
6.  Run migrations:
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

### Frontend (Next.js)

1.  Navigate into the frontend directory:
    ```bash
    cd frontend
    ```
2.  Install Node dependencies:
    ```bash
    npm install
    ```
3.  Start the development server:
    ```bash
    npm run dev
    ```

## Development

-   Frontend: [http://localhost:3000](http://localhost:3000)
-   Backend (API): [http://localhost:8000](http://localhost:8000)

## License

This project is licensed under the [MIT license](https://opensource.org/licenses/MIT).
