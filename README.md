# Habit Tracker

Aplikasi demo lokal untuk mencatat kebiasaan harian dan memantau konsistensi melalui pertumbuhan tanaman. Halaman utama menampilkan data nyata, tanpa statistik atau aktivitas buatan.

## Fitur

- Tambah, edit, hapus kebiasaan dan riwayatnya.
- Tandai selesai dan batalkan pada hari yang sama.
- Streak otomatis, progres mingguan, serta kalender aktivitas.
- Lima tahap SVG tanaman original: benih 0 hari, tunas 1–2, kecil 3–6, berkembang 7–13, dewasa 14+.

Hari mengikuti Asia/Jakarta. Streak tetap terjaga selama hari ini belum selesai jika kemarin selesai; satu hari penuh terlewat akan memutus streak. Riwayat lama tetap tersimpan. Constraint database mencegah pencatatan ganda. Progres mingguan hanya menghitung tanggal sejak kebiasaan dibuat sampai hari ini.

## Teknologi

Laravel 13, PHP 8.4.1+, SQLite, Blade, CSS, Vanilla JavaScript, dan Vite. Logika streak, kalender, serta pertumbuhan dipisahkan dalam layanan `HabitProgress`.

## Menjalankan

Diperlukan Composer, Node 22.12+, dan ekstensi PHP SQLite.

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm ci
npm run build
php artisan serve --host=127.0.0.1
```

Buka http://127.0.0.1:8000, tambahkan kebiasaan, lalu tandai saat selesai. Buka detail untuk melihat kalender dan progres mingguan. Pencatatan hanya untuk hari ini; formulir dari hari sebelumnya harus dimuat ulang.

```sh
php artisan test
npm run build
```

Aplikasi tanpa login ini hanya menerima koneksi loopback dan host lokal. Jangan mengekspos pengelolaan data ke internet tanpa autentikasi dan pembatasan akses. `.env`, database, dan log lokal tidak disertakan Git.
