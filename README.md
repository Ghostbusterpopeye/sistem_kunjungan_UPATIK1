# Sistem Kunjungan UPA TIK Polije

Sistem informasi berbasis web untuk mengelola dan mencatat data kunjungan di Unit Pelaksana Akademik (UPA) Teknologi Informasi dan Komunikasi, Politeknik Negeri Jember.

---

## Deskripsi Proyek

Aplikasi ini dikembangkan untuk menggantikan pencatatan kunjungan manual menjadi sistem digital yang lebih efisien. Sistem memungkinkan pengelolaan data pengunjung, pemantauan aktivitas, serta pembuatan laporan kunjungan.

---

## Fitur Utama

- Dashboard statistik kunjungan
- Manajemen data pengunjung
- Sistem login (admin dan petugas)
- Riwayat dan laporan kunjungan

---

## Teknologi yang Digunakan

- PHP
- MySQL
- Bootstrap
- JavaScript
- XAMPP / Laragon

---

## Instalasi dan Setup

### 1. Clone Repository
```bash
git clone https://github.com/Ghostbusterpopeye/sistem_kunjungan_UPATIK1.git
```

### 2. Setup Server
Pindahkan folder ke:
- `htdocs` (XAMPP)
- atau `www` (Laragon)

### 3. Konfigurasi Database
- Buka phpMyAdmin
- Buat database baru
- Import file dari folder:
```
database/
```

- Atur koneksi di:
```
config/
```

### 4. Jalankan Aplikasi
Akses di browser:
```
http://localhost/sistem_kunjungan_UPATIK1
```

---

## Struktur Folder

```text
/sistem_kunjungan_UPATIK1
├── assets/        # File statis (CSS, JS, gambar)
├── backend/       # Logika backend (proses data)
├── config/        # Konfigurasi database
├── database/      # File SQL / database
├── frontend/      # Tampilan user (UI)
├── includes/      # File reusable (header, footer, dll)
├── CONTRIBUTING.md
├── README.md
├── index.php      # Halaman utama
└── setup.php      # Setup awal sistem
```

---

## Kontribusi

Silakan baca file `CONTRIBUTING.md` untuk panduan kontribusi dan alur kerja tim.

---

## Tim Pengembang

Kelompok 1 F - Semester 2 - Teknik Informatika  
Politeknik Negeri Jember

---

## Catatan

- Pastikan server lokal aktif sebelum menjalankan aplikasi
- Gunakan PHP versi 7.4 atau lebih baru
