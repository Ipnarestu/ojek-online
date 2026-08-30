# Ojek Mitra Mahasiswa (OMK)

## Tools yang Dibutuhkan

### Backend
- PHP 8.1 atau lebih tinggi
- Composer 2.x
- MySQL 5.7 atau lebih tinggi
- Laravel 10.x / 11.x
- Laravel Sanctum

### Frontend
- Blade Template Engine
- Tailwind CSS
- Google Maps JavaScript API
- Google Geocoding API
- Google Distance Matrix API

### Development Environment
- XAMPP / Laragon / Valet
- Git
- Node.js & NPM (untuk asset compilation)

---

## Instalasi

# 1. Clone Repository
bash
  git clone https://github.com/username/ojek-online.git
  cd ojek-mitra-mahasiswa

2. Install Dependencies PHP
   
  composer install

4. Buat File Environment

  cp .env.example .env

4. Generate Application Key

  php artisan key:generate

6. Konfigurasi Database
Buka file .env dan sesuaikan:


  DB_CONNECTION=mysql

  DB_HOST=127.0.0.1

  DB_PORT=3306

  DB_DATABASE=nama_database

  DB_USERNAME=root

  DB_PASSWORD=

7. Install Laravel Sanctum
   
  composer require laravel/sanctum
  php artisan vendor:publish --         provider="Laravel\Sanctum\SanctumServiceProvi  der"

9. Jalankan Migration

  php artisan migrate

10. Setup Storage Link
    
  php artisan storage:link

12. Jalankan Seeder (Opsional)

  php artisan db:seed

13. Jalankan Server

  php artisan serve

  Akses aplikasi di: http://localhost:8000
