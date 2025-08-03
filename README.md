# Aplikasi Manajemen Kepegawaian Fakultas Teknik

Ini adalah aplikasi web CRUD (Create, Read, Update, Delete) sederhana yang dibangun untuk mengelola data kepegawaian di sebuah fakultas teknik. Aplikasi ini dibuat sebagai Single-Page Application (SPA), di mana semua interaksi terjadi secara dinamis tanpa perlu memuat ulang halaman.

## Presentasi Aplikasi

[<video src="presentasi-1-h265.mp4" controls="controls" style="max-width: 720px;"></video>](https://github.com/aziznrrn/pwl/blob/main/presentasi-1-h265.mp4)
[<video src="presentasi-2-database-h265.mp4" controls="controls" style="max-width: 720px;"></video>](https://github.com/aziznrrn/pwl/blob/main/presentasi-2-database-h265.mp4)

## Teknologi yang Digunakan

- **Backend**: PHP.
- **Database**: MySQL.
- **Frontend**: JavaScript.

## Fitur Utama

1. **Inisialisasi Otomatis**: Saat aplikasi dijalankan pertama kali, ia akan secara otomatis:
    - Membuat database jika belum ada.
    - Membuat tiga tabel yang diperlukan: `departments`, `lecturers`, dan `staff`.
    - Mengisi (seeding) tabel-tabel tersebut dengan data awal.
2. **Operasi CRUD Penuh**: Fungsionalitas penuh untuk menambah, melihat, mengubah, dan menghapus data untuk Dosen, Staff, dan Departemen.
3. **Single-Page Application (SPA)**: Navigasi antar halaman (Dosen, Staff, Departemen) tidak akan me-refresh browser. Konten dimuat secara dinamis menggunakan AJAX, memberikan pengalaman pengguna yang lebih cepat dan mulus.
4. **Hash-based Routing**: Status halaman tetap terjaga. Jika Anda berada di halaman "Manajemen Staff" dan me-refresh browser, Anda akan tetap berada di halaman tersebut.
5. **Konfigurasi Terpusat**: Kredensial database disimpan dalam file `.env` yang terpisah dari kode utama.

## Struktur File

Proyek ini diorganisir ke dalam file-file berikut:

- `.env`: Menyimpan kredensial dan konfigurasi database (host, nama database, user, password).
- `config.php`: Membaca konfigurasi dari file `.env`.
- `database.php`: Bertanggung jawab untuk koneksi ke database, serta logika inisialisasi dan seeding tabel.
- `index.php`: File utama yang menjadi "cangkang" atau *shell* dari SPA. Berisi navigasi dan logika untuk memuat konten halaman secara dinamis.
- `home.php`, `lecturers.php`, `staff.php`, `departments.php`: Ini adalah file "konten" yang akan dimuat ke dalam `index.php`. Masing-masing berisi struktur HTML untuk menampilkan data dan form.
- `api.php`: Endpoint tunggal yang menangani semua permintaan AJAX dari frontend. File ini berkomunikasi dengan database untuk melakukan operasi CRUD.
- `assets/main.js`: Berisi semua kode JavaScript untuk menangani interaksi pengguna, routing, memuat data dari `api.php`, dan memanipulasi DOM.

## Cara Kerja Kode

### Alur Kerja Aplikasi

1. Pengguna membuka `index.php`.
2. JavaScript di `index.php` memeriksa *hash* di URL (misalnya, `#staff`). Jika tidak ada, *hash* defaultnya adalah `#home`.
3. Berdasarkan *hash* tersebut, fungsi `loadContent` akan mengambil konten dari file yang sesuai (misalnya, `staff.php`) menggunakan `fetch`.
4. Konten dari `staff.php` disisipkan ke dalam elemen `div#content` di `index.php`.
5. Setelah konten dimuat, fungsi `loadTable('staff')` dari `assets/main.js` dipanggil.
6. `loadTable` melakukan `fetch` ke `api.php?table=staff` untuk mendapatkan data staff dalam format JSON.
7. Data yang diterima kemudian digunakan untuk membangun baris-baris tabel secara dinamis di halaman.

### Backend (`api.php`)

- File ini memeriksa metode request HTTP (`GET`, `POST`, `PUT`, `DELETE`) untuk menentukan operasi apa yang harus dilakukan.
- **`GET`**: Mengambil data dari database. Jika ada parameter `id`, ia akan mengambil satu baris data. Jika tidak, ia mengambil semua data dari tabel yang diminta. Khusus untuk tabel `lecturers`, ia melakukan `JOIN` dengan tabel `departments` untuk mendapatkan nama departemen.
- **`POST`**: Menangani pembuatan data baru. Data dikirim dari frontend dalam format JSON.
- **`PUT`**: Menangani pembaruan data yang sudah ada berdasarkan `id`.
- **`DELETE`**: Menghapus data berdasarkan `id`.

### Frontend (`assets/main.js`)

- **Routing**: Fungsi `handleRoutes` mendengarkan perubahan *hash* pada URL (`hashchange`) dan memuat konten yang sesuai.
- **Dynamic Content**: Fungsi `loadContent` bertanggung jawab untuk mengambil file HTML parsial dan menyuntikkannya ke halaman utama.
- **CRUD Asinkron**: Semua fungsi seperti `loadTable`, `showAddForm`, `showEditForm`, dan `deleteItem` menggunakan `async/await` dengan `fetch` untuk berkomunikasi dengan `api.php` tanpa memblokir antarmuka pengguna.
- **DOM Manipulation**: Kode secara aktif membuat, mengubah, dan menghapus elemen HTML (seperti baris tabel dan field form) untuk mencerminkan status aplikasi saat ini.

## Cara Menjalankan Aplikasi

1. **Salin file `.env`**: Pastikan Anda memiliki file `.env` di direktori root. Anda bisa menyalin dari contoh yang ada.
2. **Konfigurasi Database**: Buka file `.env` dan sesuaikan nilai `DB_HOST`, `DB_NAME`, `DB_USER`, dan `DB_PASS` dengan konfigurasi server MySQL Anda.
3. **Jalankan Server PHP**: Buka terminal di direktori proyek dan jalankan server pengembangan bawaan PHP:

    ```bash
    php -S localhost:8000
    ```

4. **Buka di Browser**: Buka browser web Anda dan kunjungi `http://localhost:8000`.
5. Aplikasi akan secara otomatis membuat database dan tabel jika ini adalah pertama kalinya Anda menjalankannya.
