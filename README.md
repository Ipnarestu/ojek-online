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
#bash
git clone https://github.com/username/ojek-mitra-mahasiswa.git
cd ojek-mitra-mahasiswa

2. Install Dependencies PHP
bash
composer install

3. Buat File Environment
bash
copy .env.example .env

4. Generate Application Key
bash
php artisan key:generate

5. Konfigurasi Database
Buka file .env dan sesuaikan:
.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ojek_mitra_mahasiswa
DB_USERNAME=root
DB_PASSWORD=

6. Install Laravel Sanctum
bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

7. Jalankan Migration
bash
php artisan migrate

8. Setup Storage Link
bash
php artisan storage:link

9. Jalankan Seeder (Opsional)
bash
php artisan db:seed

10. Jalankan Server
m
bash
php artisan serve
Akses aplikasi di: http://localhost:80
