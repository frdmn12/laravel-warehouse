# gudang-app (Laravel) — Cara Clone & Menjalankan

Panduan singkat menjalankan proyek Laravel di mesin lokal (Windows / Linux / macOS).

# Deskripsi Program
Aplikasi CRUD Produk / Barang Gudang dengan Sistem FIFO

# FITUR
-   AUTHENTICATION (LOGIN, REGISTER)
-   CRUD MASTER BARANG
-   VIEW STOCK BARANG
-   VIEW TRANSACTION HISTORY

## Prasyarat

-   PHP >= 8.0 (sesuaikan dengan .platform atau composer.json)
-   Composer
-   Git
-   MySQL / MariaDB (atau DB lain yang didukung)
-   Node.js + npm/yarn (untuk asset)
-   Ekstensi PHP umum: pdo, mbstring, openssl, tokenizer, xml, bcMath, fileinfo

## Clone repositori

1. Clone dari repository:

```bash
git clone <https://github.com/frdmn12/laravel-warehouse.git> gudang-app
cd gudang-app
```

## Instal dependensi PHP & JS

```bash
composer install --no-interaction --prefer-dist
npm install        # atau yarn
```

## Konfigurasi environment

1. Salin file .env

```bash
cp .env.example .env    # di Windows gunakan: copy .env.example .env
```

2. Edit .env: atur DB_DATABASE, DB_USERNAME, DB_PASSWORD, DB_HOST, APP_URL, dan pengaturan lain sesuai lingkungan.

3. Buat APP_KEY:

```bash
php artisan key:generate
```

## Database: migrasi & seeder

```bash
php artisan migrate
php artisan db:seed    # jika ada seeder
```

Jika membutuhkan refresh:

```bash
php artisan migrate:fresh --seed
```

## Link storage (untuk file yang diupload)

```bash
php artisan storage:link
```

## Menjalankan server development

```bash
php artisan serve
# default http://127.0.0.1:8000
```

Atau jalankan melalui web server (Apache/Nginx) dengan konfigurasi virtual host pointing ke folder `public/`.

## Build asset untuk development / production

Development:

```bash
npm run dev
```

Production:

```bash
npm run build    # atau npm run prod sesuai konfigurasi
```
