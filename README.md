# 📚 Student Academic Planner & Repository

Aplikasi web untuk merencanakan jadwal kuliah, mengelola tugas, dan menyimpan materi akademik.

**Teknologi:** PHP Native | MySQL | HTML5 | CSS3 | JavaScript Vanilla

---

## 🚀 Quick Start

### Prasyarat

- XAMPP (Apache + MySQL)
- PHP 7.4+
- Browser modern

### Instalasi (5 Menit)

1. **Copy folder ke XAMPP:**

   ```bash
   C:\xampp\htdocs\mhs\
   ```

2. **Aktifkan XAMPP:**
   - Buka XAMPP Control Panel
   - Start Apache & MySQL (hijau = running)

3. **Buat Database:**
   - Buka http://localhost/phpmyadmin
   - Buat database: `db_akademik`
   - Import file: `database.sql`

4. **Akses Aplikasi:**
   - Buka http://localhost/mhs
   - Login dengan akun dummy:
     - Username: `testuser`
     - Password: `password123`

---

## 📂 Struktur Folder

```
mhs/
├── css/style.css              # Styling responsif
├── js/script.js               # JavaScript (clock, reminder)
├── includes/                  # Files helper
│   ├── db.php                 # Koneksi database
│   ├── header.php
│   └── footer.php
├── uploads/tugas/             # Upload tugas
├── uploads/materi/            # Upload materi
├── index.php                  # Login/Register
├── dashboard.php              # Dashboard utama
├── tambah_jadwal.php          # Manajemen jadwal
├── tambah_tugas.php           # Manajemen tugas
├── tambah_materi.php          # Manajemen materi
├── database.sql               # SQL script
└── PANDUAN_INSTALASI.txt      # Panduan lengkap
```

---

## ✨ Fitur Utama

### 🔐 Keamanan

- ✅ Password di-hash dengan BCRYPT
- ✅ SQL Injection prevention (Prepared Statements PDO)
- ✅ Session-based authentication

### 📅 Dashboard

- ✅ Jam digital real-time
- ✅ Jadwal kuliah hari ini
- ✅ Tugas mendekati deadline
- ✅ Statistik ringkas (MK, Tugas, Progress)

### 🎓 Manajemen Jadwal

- ✅ Tambah/Edit/Hapus jadwal
- ✅ Filter by hari & jam
- ✅ Multi-view (list & grid)

### 📝 Manajemen Tugas

- ✅ Tambah tugas dengan deadline
- ✅ Upload file (PDF, DOCX, ZIP - max 10MB)
- ✅ Status tracking (Belum/Sedang/Selesai)
- ✅ Highlight urgency (overdue, soon deadline)

### 📚 Manajemen Materi

- ✅ Upload materi kuliah
- ✅ Organize by mata kuliah
- ✅ Download file materi
- ✅ Support: PDF, DOCX, PPTX, XLSX

### 🔔 Smart Reminder

- ✅ Cek setiap menit secara otomatis
- ✅ Alert 15 menit sebelum kuliah
- ✅ Browser notification + audio beep

---

## 📊 Database Schema

### users

- `id` (PK)
- `username` (UNIQUE)
- `password_hash` (BCRYPT)
- `created_at`

### matakuliah

- `id` (PK)
- `user_id` (FK)
- `nama_mk`
- `dosen`
- `hari` (Senin-Minggu)
- `jam_mulai` / `jam_selesai`
- `ruang`

### tugas

- `id` (PK)
- `user_id` (FK)
- `matakuliah_id` (FK)
- `judul` / `deskripsi`
- `deadline`
- `status` (Belum/Sedang/Selesai/Terlambat)
- `file_path` (optional)

### materi

- `id` (PK)
- `user_id` (FK)
- `matakuliah_id` (FK)
- `judul`
- `file_path`

---

## 🎨 Design Features

- ✅ **Responsive Design** (Mobile, Tablet, Desktop)
- ✅ **Modern UI** (Gradient, Flexbox/Grid, Smooth Animations)
- ✅ **Dark-friendly** (Light background, good contrast)
- ✅ **Accessible** (Semantic HTML, ARIA labels)
- ✅ **Fast Loading** (Inline CSS, minimal JS)

---

## 📖 Dokumentasi Lengkap

Baca file `PANDUAN_INSTALASI.txt` untuk:

- Langkah instalasi detail
- User guide lengkap
- Troubleshooting
- Tips & trik
- FAQ

---

## ⚙️ Konfigurasi

Edit file `includes/db.php` jika menggunakan username/password MySQL yang berbeda:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_akademik');
```

---

## 🔗 URL Routes

| URL                      | Function         |
| ------------------------ | ---------------- |
| `/mhs/`                  | Login/Register   |
| `/mhs/dashboard.php`     | Dashboard        |
| `/mhs/tambah_jadwal.php` | Manajemen Jadwal |
| `/mhs/tambah_tugas.php`  | Manajemen Tugas  |
| `/mhs/tambah_materi.php` | Manajemen Materi |
| `/mhs/logout.php`        | Logout           |

---

## 🐛 Troubleshooting Cepat

| Error                    | Solusi                                        |
| ------------------------ | --------------------------------------------- |
| "Gagal koneksi database" | Cek Apache & MySQL running, cek db.php config |
| "Database doesn't exist" | Import database.sql di phpMyAdmin             |
| "Upload gagal"           | Cek folder uploads/ permissions (chmod 755)   |
| "Blank page"             | Cek error log: C:\xampp\apache\logs\error.log |

---

## 📝 Testing Credentials

**Dummy Account:**

- Username: `testuser`
- Password: `password123`

Atau buat akun baru sendiri dengan tombol "Daftar".

---

## 📋 File Upload Limits

- **Tugas:** Max 10 MB (PDF, DOCX, ZIP, DOC, TXT)
- **Materi:** Max 20 MB (PDF, DOCX, PPTX, XLSX, DOC, TXT)

---

## 🎯 Best Practices

- ✅ Logout setelah selesai menggunakan
- ✅ Jangan share password aplikasi
- ✅ Backup database secara berkala
- ✅ Buat jadwal terlebih dahulu sebelum menambah tugas
- ✅ Gunakan deadline yang akurat untuk reminder

---

## 📄 License

Bebas untuk digunakan, dimodifikasi, dan didistribusikan.

---

## 👨‍💻 Dibuat oleh

**Senior Web Developer** - dengan ❤️ menggunakan PHP Native & MySQL

Selamat belajar! 📚🎓

---

**Last Updated:** February 2026
