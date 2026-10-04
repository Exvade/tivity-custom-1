# Senandika — Undangan Levi & Dio

Undangan digital responsif bernuansa maroon, hitam, dan emas. Dibuat dengan Vite, JavaScript, CSS, Lucide, dan Lottie Web. Tidak menggunakan framework atau layanan eksternal untuk menjalankan halaman.

## Menjalankan

```sh
npm install
npm run dev
```

Buka `http://localhost:5173`. Pada PowerShell yang memblokir `npm.ps1`, gunakan `npm.cmd run dev`.

```sh
npm run build
npm run preview
```

Hasil produksi tersedia di `dist/`, siap untuk hosting statis pada root domain.

## Mengganti data

- `src/config.js`: nama, keluarga, tanggal, kota, lokasi/peta, waktu akad/resepsi, rekening, alamat kado, tautan streaming, dan vendor.
- Sesuaikan `date`, `dateLabel`, serta `start`/`end` setiap acara. Nilai kalender menggunakan UTC; 08.00 WIB = 01.00 UTC.
- Ganti foto `public/images/wedding.jpg`, `moment.jpg`, dan `ceremony.jpg` dengan foto pasangan. Sesuaikan posisi crop di `src/style.css` jika diperlukan.
- Cerita pasangan dan daftar galeri berada di `src/main.js`.
- Metadata awal untuk pratinjau tautan dan favicon berada di `index.html` serta `public/favicon.svg`.
- Personalisasi nama tamu melalui `/?to=Nadia%20%26%20Keluarga`.
- `demo: true` menampilkan penanda rekening/alamat contoh. Ganti seluruh data sebelum menonaktifkannya.

## Fitur

Pembuka, ayat dan salam, profil pasangan, countdown, akad/resepsi, peta, unduh kalender ICS, informasi streaming, cerita pasangan, galeri lightbox, hadiah transfer/kado dengan salin clipboard, RSVP/ucapan, catatan tamu, keluarga, vendor, dan penutup.

Lottie dimuat saat mendekati layar dan berhenti saat di luar layar. Tersedia tombol jeda animasi dan dukungan `prefers-reduced-motion`. Musik ambient sintetis original dimulai setelah klik Buka Undangan atau tombol musik. Font, foto, dan aset animasi disimpan lokal.

## RSVP

Versi ini adalah frontend. Secara default, ucapan hanya tersimpan di `localStorage` browser pengisi (maksimal 50 ucapan), sehingga belum diterima mempelai dan tidak terlihat oleh tamu lain. Tiga ucapan awal diberi label sebagai contoh.

Isi `rsvpEndpoint` dengan endpoint server milik Anda untuk mengirim POST JSON:

```json
{"name":"Nama tamu","attendance":"yes","guests":2,"message":"Selamat!","createdAt":"2026-10-03T00:00:00.000Z"}
```

Server harus memvalidasi input, menyimpan data, memberi respons HTTP 2xx setelah berhasil, dan mengizinkan origin situs bila berbeda domain. Tampilan ucapan bersama memerlukan API pembacaan tambahan; belum disediakan dalam frontend ini. Tautan streaming kosong akan menampilkan informasi jadwal, bukan membuka tautan contoh.

## Pemeriksaan

Dengan dev server aktif dan Google Chrome terpasang:

```sh
node qa.mjs
```

Pemeriksaan integrasi mencakup viewport desktop/ponsel, Lottie, nama tamu, kontrol musik/animasi, clipboard, tab hadiah, ICS, streaming, galeri, RSVP, persistensi, escaping input, dan reduced motion. Screenshot pemeriksaan tersimpan di `test-results/`.

## Sumber aset

- Referensi section: https://luxee.net/premium/tema-06/
- Lottie dari folder Luxee lokal yang disediakan pemilik proyek: `JSON LUXEE 1/bunga 1.json`, `JSON LUXEE 6/footer.json`, dan `JSON LUXEE 13/bunga1.json`. Hak aset tetap milik Luxee/Levidio; penggunaan mengikuti lisensi pembelian pemilik proyek. Aset ini bukan paket untuk didistribusikan ulang.
- Foto ilustrasi: Unsplash (`photo-1519741497674-611481863552`, `photo-1511285560929-80b456fea0bc`, `photo-1523438885200-e635ba2c371e`). Ganti dengan foto pasangan untuk undangan final.
- Font Google Fonts: Cormorant Garamond, DM Sans, dan Italianno. Lisensi SIL OFL disertakan dalam `public/fonts/`.
- Ikon Lucide (ISC) dan lottie-web (MIT) melalui npm.
