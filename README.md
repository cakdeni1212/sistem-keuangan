# Sistem Keuangan - Forka Coffee & Space

Sistem manajemen keuangan untuk Forka Coffee & Space.

## Setup
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
```

## Update
```bash
git pull
php artisan migrate
php artisan optimize
```

