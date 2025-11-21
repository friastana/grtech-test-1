# GRTECH Technical Test 1

Company and Employee Management for GRTECH Technical Test 1.

## Installation

Use [Docker](https://www.docker.com/) and WSL 2 for Windows.

1. Copy .env.example to .env
2. Run
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

./vendor/bin/sail up

npm install --legacy-peer-deps

php artisan key:generate

php artisan migrate

php artisan db:seed
```

## Usage

1. Run
```bash
npm run dev
```
