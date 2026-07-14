# SIMBING — Web-App (Laravel Backend & Web Panel)

Web-App SIMBING berfungsi sebagai backend utama (menyediakan REST API dengan Laravel Sanctum) sekaligus antarmuka web responsif (menggunakan Laravel Blade + Livewire + TailwindCSS) untuk manajemen bimbingan skripsi.

---

## 📋 1. Analisis Kebutuhan Sistem

### 1.1 Kebutuhan Perangkat Lunak (Prerequisites)
Untuk menjalankan web-app ini secara lokal, pastikan komputer Anda telah terinstal:
- **PHP**: Versi `>= 8.3`
- **Composer**: Untuk manajemen dependensi PHP
- **Node.js**: Versi `>= 18` & **npm** (untuk build TailwindCSS v4 & Vite)
- **Database**: PostgreSQL (direkomendasikan untuk produksi/pengembangan lokal) atau SQLite (untuk development fallback)
- **Web Browser**: Chrome, Firefox, Edge, atau browser modern lainnya

### 1.2 Kebutuhan Fungsional (Berdasarkan Role)

#### A. Mahasiswa (Student)
- Melakukan pendaftaran/login mandiri atau menggunakan Akun Google.
- Melengkapi data profil mahasiswa (NIM, Jurusan, Fakultas, Semester) saat pertama kali masuk (Onboarding).
- Memilih Dosen Pembimbing yang tersedia.
- Mengirim berkas bimbingan/skripsi (PDF/DOC) beserta usulan jadwal konsultasi (tanggal, waktu, tipe online/offline).
- Melihat riwayat bimbingan, timeline status, dan progress skripsi dalam persentase (%).
- Mengirim komentar di thread diskusi revisi.
- Mengubah jadwal konsultasi (reschedule) atau membatalkan konsultasi.

#### B. Dosen (Lecturer)
- Melakukan pendaftaran/login mandiri atau menggunakan Akun Google.
- Melengkapi data profil dosen (NIDN, Jurusan, Fakultas, Spesialisasi Keahlian).
- Melihat daftar mahasiswa bimbingan aktif beserta statistik bimbingan mereka.
- Menerima notifikasi bimbingan baru.
- Membuka berkas bimbingan mahasiswa dan memberikan keputusan review (*Approved*, *Revision Minor*, *Revision Major*, *Rejected*) beserta feedback tertulis.
- Menyetujui, menjadwalkan ulang, atau menolak usulan pertemuan konsultasi (dengan menyertakan alasan penolakan).
- Mengirim komentar di thread diskusi revisi dan menandainya sebagai selesai (*Resolved*).

### 1.3 Kebutuhan Non-Fungsional
- **Keamanan**: Autentikasi berbasis session untuk web, dan token bearer menggunakan Laravel Sanctum untuk aplikasi mobile. Kebijakan otorisasi diimplementasikan ketat menggunakan Laravel Policy & Gate.
- **Notifikasi Asinkron**: Pengiriman email menggunakan antrean database (*database queue driver*) agar performa request tetap cepat dan responsif.
- **Desain Responsif**: Antarmuka web dioptimalkan untuk perangkat mobile dan desktop menggunakan TailwindCSS.

---

## ✨ 2. Fitur Utama Web-App

1. **Autentikasi Multi-Metode**: Login konvensional dengan Email & Password atau login instan menggunakan Google OAuth (Socialite).
2. **Sistem Onboarding Dinamis**: Mengarahkan pengguna baru untuk melengkapi profil khusus sesuai role (*Lecturer* / *Student*).
3. **Pengajuan Bimbingan & Milestone Otomatis**: Mahasiswa mengunggah file bimbingan (maksimal 10MB) dan sistem secara otomatis menentukan tipe dokumen (*Proposal*, *Bab*, atau *Revisi*) berdasarkan analisis string judul dokumen.
4. **Alur Keputusan Review Dosen**: Memperbarui status kelayakan dokumen dan secara dinamis memperbarui persentase progress skripsi mahasiswa.
5. **Thread Diskusi Interaktif (Revisi Chat)**: Ruang komunikasi real-time per dokumen antara mahasiswa dan dosen pembimbing untuk meminimalkan miskomunikasi.
6. **Manajemen Jadwal Konsultasi**: Penjadwalan pertemuan terintegrasi dengan opsi konfirmasi/penolakan oleh dosen serta reschedule/pembatalan oleh mahasiswa.
7. **Perhitungan Progress Kelulusan**: Progress dihitung otomatis berdasarkan jumlah bab/proposal unik yang telah disetujui (1 Proposal + 5 Bab = 6 milestone utama, masing-masing bernilai ~16.6%).
8. **Double Notification Dispatcher**:
   - **Email Notifikasi (SMTP)**: Terkirim secara background (queued) saat pendaftaran, pengajuan baru, keputusan review, dan pembaruan jadwal.
   - **Expo Push Notification API**: Mengirim push notification langsung ke handphone mahasiswa/dosen yang terintegrasi dengan Mobile-App.

---

## ⚙️ 3. Langkah-Langkah Konfigurasi & Instalasi

Ikuti langkah-langkah di bawah ini untuk mengonfigurasi proyek dari awal:

### Langkah 1: Clone & Masuk ke Direktori
Buka terminal Anda dan jalankan perintah berikut:
```bash
git clone https://github.com/username/Manajemen-skripsi.git
cd Manajemen-skripsi/web-app
```

### Langkah 2: Instal Dependensi
Instal pustaka backend PHP dan frontend Javascript:
```bash
composer install
npm install
```

### Langkah 3: Setup Environment File (`.env`)
Salin file template `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

Buka file `.env` yang baru dibuat menggunakan text editor Anda, lalu sesuaikan konfigurasi berikut:

#### 1. Konfigurasi Database (PostgreSQL / SQLite)
Jika Anda menggunakan **PostgreSQL**:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nama_database_anda
DB_USERNAME=postgres
DB_PASSWORD=password_postgres_anda
```
Jika Anda ingin menggunakan **SQLite** untuk uji coba cepat:
```env
DB_CONNECTION=sqlite
# Kosongkan atau komentari DB_HOST, DB_PORT, DB_DATABASE, dll.
```
*(Catatan: Jika memilih SQLite, buat file kosong database di `database/database.sqlite`)*

#### 2. Konfigurasi Google OAuth (Socialite)
Dapatkan Client ID & Client Secret dari [Google Cloud Console](https://console.cloud.google.com/). Daftarkan URL callback berikut di Google Console: `http://localhost:8000/auth/google/callback`.
Isi variabel berikut di `.env`:
```env
GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

#### 3. Konfigurasi Email (Mailtrap untuk Development)
Daftar di [Mailtrap](https://mailtrap.io) untuk mendapatkan inbox percobaan SMTP. Salin kredensialnya ke `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=username-mailtrap-anda
MAIL_PASSWORD=password-mailtrap-anda
MAIL_FROM_ADDRESS="noreply@simbing.ac.id"
MAIL_FROM_NAME="SIMBING - Sistem Bimbingan Skripsi"
```

#### 4. Driver Antrean & Session (Wajib Diubah)
Untuk mengaktifkan sistem antrean email dan penyimpanan sesi database:
```env
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

### Langkah 4: Generate Application Key
Jalankan perintah untuk membuat kunci pengaman Laravel:
```bash
php artisan key:generate
```

### Langkah 5: Migrasi Database & Seeding
Jalankan migrasi untuk membuat tabel sistem dan mengisi data awal (seperti dosen default, admin, dll.):
```bash
php artisan migrate --seed
```

### Langkah 6: Membuat Symbolic Link Storage
Agar file PDF/DOC bimbingan yang diunggah oleh mahasiswa dapat diunduh oleh dosen, hubungkan folder storage ke folder public:
```bash
php artisan storage:link
```

---

## 🚀 4. Menjalankan Aplikasi

Aplikasi ini menggunakan skrip kustom untuk mempermudah eksekusi server pengembangan, worker antrean, pembaca log, dan Vite compiler sekaligus.

### Cara Praktis (Menggunakan Concurrently)
Jalankan perintah ini untuk memulai semua proses sekaligus:
```bash
composer dev
```
Perintah ini akan secara otomatis menjalankan:
1. **Server Utama** di `http://127.0.0.1:8000`
2. **Vite Development Server** untuk hot-reload CSS/JS
3. **Queue Listener** (`queue:listen`) agar email notifikasi langsung terkirim
4. **Pail Log Viewer** untuk memantau log aplikasi secara langsung

### Cara Manual (Menjalankan Satu per Satu di Terminal Terpisah)
Jika Anda ingin menjalankannya secara terpisah di terminal yang berbeda:

1. **Jalankan Web Server**:
   ```bash
   php artisan serve --host=0.0.0.0
   ```
2. **Jalankan Vite Compiler**:
   ```bash
   npm run dev
   ```
3. **Jalankan Background Queue Worker**:
   ```bash
   php artisan queue:listen
   ```

---

## 💡 5. Catatan Penting Pengoperasian & Troubleshooting

- **Email Tidak Masuk**: Semua email menggunakan antrean (`ShouldQueue`). Jika email tidak terkirim, pastikan worker antrean (`php artisan queue:listen`) sudah aktif berjalan.
- **Koneksi Aplikasi Mobile (API)**: Agar Mobile-App dapat terhubung ke backend ini saat dijalankan di device fisik, pastikan Anda mengubah URL API pada file konfigurasi mobile (`services/api.ts`) ke IP lokal komputer host (misal: `http://192.168.1.10:8000`), bukan `localhost` atau `127.0.0.1`.
- **Registrasi Google Tanpa Password**: Pengguna yang terdaftar via Google login memiliki field `password` bernilai `null`. Mereka login murni menggunakan Google Sign-In.
- **File Upload Limits**: Jika mengunggah berkas bimbingan berukuran besar gagal, periksa konfigurasi `upload_max_filesize` dan `post_max_size` pada file `php.ini` sistem Anda.
