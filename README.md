# Warehouse Control System (Kontrolling Gudang)

Sistem kontrol gudang berbasis web yang dibangun dengan Laravel 12. Sistem ini memungkinkan Super Admin untuk membuat dan mengelola To Do Lists, serta Supervisor untuk menginput laporan harian berdasarkan To Do Lists yang ditugaskan.

## Fitur Utama

### Super Admin
- ✅ CRUD To Do Lists dengan berbagai tipe (Man Power, Finish Good, Raw Material, Gudang, Supplier Datang)
- ✅ Assign To Do Lists ke Supervisor
- ✅ View dan filter laporan berdasarkan tanggal, task, dan supervisor
- ✅ Export laporan ke Excel
- ✅ CRUD Supervisor users
- ✅ Indikator due date untuk To Do Lists

### Supervisor
- ✅ View To Do Lists yang ditugaskan (card layout)
- ✅ Input laporan harian berdasarkan tipe To Do List
- ✅ Filter To Do Lists berdasarkan due date dan task
- ✅ View dan edit laporan yang sudah dibuat
- ✅ To Do Lists yang sudah dilaporkan hari ini tidak muncul lagi di daftar

## Requirements

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM (untuk asset compilation)
- Extension PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## Setup di aapanel

### 1. Persiapan di aapanel

1. **Buat Website Baru**
   - Login ke aapanel
   - Klik "Website" → "Add Site"
   - Isi domain/subdomain Anda
   - Pilih PHP version (minimal PHP 8.2)
   - Pilih "Create Database" dan catat informasi database

2. **Upload File Project**
   - Gunakan File Manager di aapanel atau upload via FTP/SFTP
   - Upload semua file project ke folder website Anda (biasanya `/www/wwwroot/yourdomain.com`)

### 2. Install Dependencies

**Via SSH Terminal di aapanel:**

```bash
# Masuk ke direktori project
cd /www/wwwroot/yourdomain.com

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install NPM dependencies (jika diperlukan)
npm install
npm run build
```

**Atau via Terminal di aapanel:**
- Buka "Terminal" di aapanel
- Pilih website Anda
- Jalankan command di atas

### 3. Konfigurasi Environment

1. **Copy file .env**
   ```bash
   cp .env.example .env
   ```

2. **Edit file .env** via File Manager atau Terminal:
   ```env
   APP_NAME="Warehouse Control System"
   APP_ENV=production
   APP_KEY=
   APP_DEBUG=false
   APP_URL=http://yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=username_database
   DB_PASSWORD=password_database
   ```

3. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

### 4. Setup Database

1. **Buat Database di aapanel**
   - Klik "Database" → "Add Database"
   - Catat nama database, username, dan password

2. **Update .env dengan informasi database**

3. **Jalankan Migration dan Seeder**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=WarehouseSystemSeeder
   ```

### 5. Set Permissions

**Via Terminal:**
```bash
# Set ownership (sesuaikan user dengan aapanel)
chown -R www:www storage bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache
```

**Atau via File Manager:**
- Klik kanan folder `storage` → Properties → Set permissions ke `775`
- Klik kanan folder `bootstrap/cache` → Properties → Set permissions ke `775`

### 6. Konfigurasi Web Server

**Jika menggunakan Nginx (default aapanel):**

1. Edit konfigurasi website di aapanel:
   - Klik "Website" → Pilih website Anda → "Settings" → "Configuration File"

2. Pastikan konfigurasi seperti ini:
   ```nginx
   location / {
       try_files $uri $uri/ /index.php?$query_string;
   }

   location ~ \.php$ {
       fastcgi_pass unix:/tmp/php-cgi-82.sock; # Sesuaikan versi PHP
       fastcgi_index index.php;
       fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
       include fastcgi_params;
   }
   ```

**Jika menggunakan Apache:**
- Pastikan mod_rewrite sudah aktif
- File `.htaccess` sudah ada di root project

### 7. Optimasi untuk Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 8. Setup Cron Job (Opsional)

Untuk task scheduler Laravel, tambahkan cron job di aapanel:
- Klik "Cron" → "Add Cron"
- Command: `php /www/wwwroot/yourdomain.com/artisan schedule:run`
- Time: `* * * * *` (setiap menit)

### 9. Akses Aplikasi

1. Buka browser dan akses domain Anda
2. Login dengan kredensial default:
   - **Super Admin:**
     - Email: `admin@gmail.com`
     - Password: `password`
   - **Supervisor:**
     - Email: `supervisor1@gmail.com` - `supervisor5@gmail.com`
     - Password: `password`

## Default Credentials (Setelah Seeder)

### Super Admin
- Email: `admin@gmail.com`
- Password: `password`

### Supervisor
- Email: `supervisor1@gmail.com` sampai `supervisor5@gmail.com`
- Password: `password`

**⚠️ PENTING: Ganti password default setelah setup pertama kali!**

## Struktur Database

- `users` - Tabel user (Super Admin & Supervisor)
- `todo_lists` - Tabel To Do Lists
- `todo_items` - Tabel items dalam To Do List
- `todo_assignments` - Pivot table untuk assignment To Do List ke Supervisor
- `daily_reports` - Tabel laporan harian
- `report_man_power` - Data Man Power
- `report_stock_finish_good` - Data Stock Finish Good
- `report_stock_raw_material` - Data Stock Raw Material
- `report_warehouse_condition` - Data Kondisi Gudang
- `report_suppliers` - Data Supplier Datang

## Troubleshooting

### Error: Permission denied
```bash
chmod -R 775 storage bootstrap/cache
chown -R www:www storage bootstrap/cache
```

### Error: Class not found
```bash
composer dump-autoload
php artisan optimize:clear
```

### Error: 500 Internal Server Error
- Cek file `.env` sudah benar
- Cek permissions folder `storage` dan `bootstrap/cache`
- Cek error log di aapanel: "Website" → "Logs"

### Error: Database connection failed
- Pastikan informasi database di `.env` benar
- Pastikan database sudah dibuat di aapanel
- Cek user database memiliki permission yang cukup

## Development

### Local Development Setup

```bash
# Clone repository
git clone [repository-url]

# Install dependencies
composer install
npm install

# Copy .env
cp .env.example .env

# Generate key
php artisan key:generate

# Setup database di .env
# Lalu jalankan:
php artisan migrate
php artisan db:seed --class=WarehouseSystemSeeder

# Run development server
php artisan serve
```

## Teknologi yang Digunakan

- **Framework:** Laravel 12
- **Authentication:** Laravel Breeze (Blade Stack)
- **UI:** Tailwind CSS
- **Database:** MySQL/MariaDB
- **Excel Export:** Maatwebsite/Excel
- **Primary Keys:** UUID

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
