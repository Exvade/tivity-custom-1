# Tivity Custom Invitation

Undangan digital fullstack berbasis Laravel 13, MySQL, Blade, Vite, Lottie, dan JavaScript. Aplikasi mendukung URL berbasis slug, personalisasi nama tamu, RSVP, ucapan publik, generator link tamu, login customer, dan moderasi ucapan.

## Kebutuhan

- PHP 8.3 atau lebih baru
- Composer 2
- MySQL/MariaDB
- Node.js 20 atau lebih baru
- Ekstensi PHP: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `intl`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, dan `zip`

## Instalasi lokal

```sh
composer install
npm install
copy .env.example .env
php artisan key:generate
```

Atur koneksi database dan akun customer pada `.env`:

```dotenv
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=undangan_digital_alya
DB_USERNAME=root
DB_PASSWORD=

ADMIN_NAME="Nama Customer"
ADMIN_EMAIL=customer@example.com
ADMIN_PASSWORD="password-kuat-dan-unik"
```

Lanjutkan dengan:

```sh
php artisan migrate --seed
npm run build
php artisan serve
```

Halaman penting:

- Undangan: `http://localhost:8000/alya-dan-salman`
- Login customer: `http://localhost:8000/admin/login`
- Dashboard: `http://localhost:8000/dashboard`

## Alur ucapan dan moderasi

Mode default adalah `manual`. Ucapan baru masuk dengan status `pending` dan hanya tampil setelah customer memilih **Tampilkan** dari dashboard. Mode dapat diganti menjadi `hybrid`; ucapan bersih akan langsung tampil sedangkan teks yang terindikasi kasar, spam, atau memuat tautan tetap ditahan.

Perlindungan yang diterapkan:

- validasi nama, kehadiran, jumlah tamu, dan panjang ucapan;
- output tamu selalu di-escape;
- CSRF protection;
- honeypot bot;
- rate limit per undangan dan alamat IP;
- normalisasi kata tersamar sebelum pemeriksaan;
- pemisahan data berdasarkan pemilik undangan;
- token tamu acak dan batas pengiriman per token;
- hash IP, bukan alamat IP mentah.

Daftar kata dan pola moderasi berada di `config/moderation.php`. Hasil filter hanya menahan ucapan untuk diperiksa, tidak menghapusnya otomatis.

## Generator nama tamu

Customer dapat membuka **Dashboard → Link Tamu**, lalu memasukkan satu nama per baris. Link yang dihasilkan berbentuk:

```text
https://domain.com/alya-dan-salman?guest=TOKEN&to=Bapak%20Budi
```

Token mengunci identitas tamu pada server. Mengubah parameter `to` tidak akan mengubah nama yang disimpan saat mengirim ucapan.

## Struktur data

- `users`: akun customer.
- `invitations`: slug, pemilik, pengaturan moderasi, dan konten undangan JSON.
- `guests`: nama tamu, token unik, dan batas pengiriman.
- `wishes`: RSVP, ucapan, status moderasi, alasan penandaan, dan moderator.

Data contoh undangan dibuat di `database/seeders/DatabaseSeeder.php`. Ganti data mempelai, tanggal, acara, rekening, lokasi, keluarga, dan vendor sebelum deployment produksi. Foto berada di `public/images`; Lottie berada di `public/lottie`.

## Pengujian

```sh
php artisan test
npm run build
```

Untuk pengujian browser end-to-end, jalankan server terlebih dahulu:

```sh
php artisan serve
node qa.mjs
```

Pengujian mencakup penyimpanan ucapan, pending moderation, login customer, persetujuan ucapan, tampilan publik, generator token, isolasi customer, Lottie, clipboard, tampilan ponsel, dan reduced motion.

## Deployment Hostinger

1. Atur subdomain/domain dengan document root menuju folder `public` Laravel.
2. Upload proyek atau clone repository melalui SSH.
3. Buat `.env` produksi dengan `APP_ENV=production`, `APP_DEBUG=false`, URL domain, serta kredensial MySQL Hostinger.
4. Jalankan `composer install --no-dev --optimize-autoloader`.
5. Jalankan `npm ci && npm run build` secara lokal atau di server jika Node.js tersedia; folder `public/build` harus ikut deployment.
6. Jalankan `php artisan key:generate`, `php artisan migrate --force`, dan `php artisan db:seed --force` saat instalasi awal.
7. Jalankan `php artisan config:cache`, `php artisan route:cache`, dan `php artisan view:cache`.
8. Pastikan `storage` dan `bootstrap/cache` dapat ditulis oleh PHP.

Jangan menjalankan seeder berulang kali setelah data undangan diedit dari database tanpa meninjau efeknya, karena seeder memperbarui konten undangan contoh. Jangan pernah mengunggah `.env` ke GitHub.

## Lisensi aset

Lottie berasal dari folder Luxee lokal yang diberikan pemilik proyek. Penggunaan harus mengikuti lisensi pembelian Luxee/Levidio dan aset tidak boleh didistribusikan ulang sebagai produk generator/SaaS. Font memakai Cormorant Garamond, DM Sans, dan Italianno dengan lisensi SIL OFL yang disertakan di `public/fonts`.
