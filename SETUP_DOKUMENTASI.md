# 📘 Dokumentasi Setup Project PKL - QC Dokumen dengan AI

Sistem Quality Control untuk membandingkan dokumen Proposal dengan Laporan menggunakan AI (Google Gemini).

---

## 🎯 Fitur Utama

- ✅ Upload dokumen Proposal dan Laporan (PDF)
- ✅ Ekstraksi teks otomatis dari PDF
- ✅ Analisis perbandingan menggunakan AI Gemini
- ✅ Deteksi ketidaksesuaian (MATCH/MISMATCH)
- ✅ Status QC otomatis (APPROVED/REJECTED)
- ✅ Riwayat audit tersimpan di database

---

## 📋 Requirements

Pastikan software berikut sudah terinstall di komputer:

### 1. PHP >= 8.2
```bash
# Cek versi PHP
php -v
```

**Jika belum ada:**
- Download [XAMPP](https://www.apachefriends.org/) atau [Laragon](https://laragon.org/)
- Atau install PHP standalone dari [windows.php.net](https://windows.php.net/download/)

### 2. Composer
```bash
# Cek versi Composer
composer -v
```

**Jika belum ada:**
- Download dari [getcomposer.org](https://getcomposer.org/download/)
- Install dengan default settings

### 3. PostgreSQL (Database)
```bash
# Cek apakah PostgreSQL running
psql --version
```

**Jika belum ada:**
- Download [PostgreSQL](https://www.postgresql.org/download/windows/)
- Install dengan username dan password sesuai preferensi Anda
- Jangan lupa **start PostgreSQL service**

**Alternatif:** Bisa pakai **MySQL** jika lebih familiar.

### 4. Node.js & NPM (untuk frontend assets)
```bash
# Cek versi Node.js
node -v
npm -v
```

**Jika belum ada:**
- Download [Node.js LTS](https://nodejs.org/)
- NPM otomatis terinstall bersama Node.js

### 5. Git (opsional, untuk clone project)
```bash
git --version
```

**Jika belum ada:**
- Download [Git for Windows](https://git-scm.com/download/win)

---

## 🚀 Langkah Instalasi

### Step 1: Clone/Download Project

**Opsi A - Jika ada Git:**
```bash
git clone [URL_REPOSITORY]
cd pekael
```

**Opsi B - Manual:**
- Download ZIP project
- Extract ke folder, misal: `D:\KULIAH\PKL\pekael`
- Buka terminal/CMD di folder tersebut

---

### Step 2: Install Dependencies PHP

```bash
composer install
```

**Jika error "composer not found":**
- Restart terminal setelah install Composer
- Atau gunakan full path: `C:\ProgramData\ComposerSetup\bin\composer.bat install`

**Output yang benar:**
```
Installing dependencies from lock file
...
Package operations: 100+ installs
...
Generating optimized autoload files
```

---

### Step 3: Install Dependencies JavaScript

```bash
npm install
```

**Output yang benar:**
```
added 500+ packages
```

---

### Step 4: Setup Environment

1. **Copy file `.env.example` menjadi `.env`:**
   ```bash
   cp .env.example .env
   ```
   
   **Di Windows (jika `cp` tidak ada):**
   ```bash
   copy .env.example .env
   ```

2. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```
   
   **Output:**
   ```
   INFO  Application key set successfully.
   ```

3. **Edit file `.env` dengan text editor** (Notepad++, VS Code, dll):

   **Untuk PostgreSQL:**
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=nama_database_anda
   DB_USERNAME=username_anda
   DB_PASSWORD=password_anda
   ```

   **Untuk MySQL:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=username_anda
   DB_PASSWORD=password_anda
   ```

4. **Setup Gemini API Key:**
   
   a. Buka [Google AI Studio](https://aistudio.google.com/app/apikey)
   
   b. Login dengan akun Google
   
   c. Klik **"Create API Key"** atau **"Get API Key"**
   
   d. Copy API key yang digenerate
   
   e. Paste ke file `.env`:
   ```env
   GEMINI_API_KEY=your_api_key_here
   ```

   **⚠️ PENTING:**
   - Gemini API **GRATIS** untuk Gemini 1.5 Flash
   - Limit: 15 requests/menit, 1500 requests/hari
   - Jangan share API key ke publik (masukkan ke `.gitignore`)

---

### Step 5: Setup Database

1. **Buat database baru:**

   **PostgreSQL:**
   ```bash
   # Masuk ke PostgreSQL (sesuaikan username Anda)
   psql -U your_username
   
   # Buat database (sesuaikan nama database)
   CREATE DATABASE your_database_name;
   
   # Keluar
   \q
   ```

   **MySQL:**
   ```bash
   # Masuk ke MySQL (sesuaikan username Anda)
   mysql -u your_username -p
   
   # Buat database (sesuaikan nama database)
   CREATE DATABASE your_database_name;
   
   # Keluar
   exit;
   ```

   **Atau pakai GUI:**
   - **pgAdmin 4** (untuk PostgreSQL)
   - **phpMyAdmin** (untuk MySQL)

2. **Jalankan Migration:**
   ```bash
   php artisan migrate
   ```

   **Output yang benar:**
   ```
   INFO  Running migrations.
   
   2014_10_12_000000_create_users_table ......... DONE
   2026_01_09_081148_create_kegiatan_table ...... DONE
   2026_01_09_081151_create_dokumen_table ....... DONE
   2026_01_09_081242_create_audit_logs_table .... DONE
   ```

   **Jika error "Connection refused":**
   - Pastikan PostgreSQL/MySQL service running
   - Cek username & password di `.env`
   - Cek apakah database sudah dibuat sesuai nama di `.env`

---

### Step 6: Setup Storage & Permissions

```bash
# Buat symbolic link untuk storage
php artisan storage:link

# Pastikan folder storage writable
# (Biasanya otomatis di Windows, tapi cek jika ada error)
```

---

### Step 7: Compile Frontend Assets

**Development mode (dengan auto-reload):**
```bash
npm run dev
```
Biarkan terminal ini tetap berjalan.

**Production mode (sekali compile):**
```bash
npm run build
```

---

## ▶️ Menjalankan Aplikasi

### Cara 1: Artisan Serve (Paling Mudah)

**Terminal 1 - Backend:**
```bash
php artisan serve
```

**Output:**
```
INFO  Server running on [http://127.0.0.1:8000]
```

**Terminal 2 - Frontend (jika perlu live reload):**
```bash
npm run dev
```

Buka browser: **http://127.0.0.1:8000**

---

### Cara 2: Menggunakan XAMPP/Laragon

1. Copy folder project ke `htdocs` (XAMPP) atau `www` (Laragon)
2. Buat virtual host atau akses via: `http://localhost/pekael/public`
3. Pastikan `.env` sudah dikonfigurasi

---

## 🧪 Testing Aplikasi

### 1. Register/Login

- Akses: http://127.0.0.1:8000/register
- Buat akun baru
- Login dengan akun tersebut

### 2. Upload Dokumen

- Klik menu **"Dashboard"** atau akses `/dashboard`
- Isi form:
  - **Judul Kegiatan:** Contoh: "Seminar IT 2026"
  - **File Proposal:** Upload PDF proposal
  - **File Laporan:** Upload PDF laporan
- Klik **"🚀 Upload & Cek Otomatis"**

### 3. Lihat Hasil QC

- Sistem akan otomatis:
  1. Extract teks dari kedua PDF
  2. Simpan ke database (tabel `kegiatan` & `dokumen`)
  3. Kirim ke Gemini AI untuk analisis
  4. Simpan hasil ke `audit_logs`
  5. Update status QC (APPROVED/REJECTED)
- Redirect ke halaman hasil dengan badge status dan analisis AI

---

## 📁 Struktur Database

### Tabel `kegiatan`
| Kolom | Type | Deskripsi |
|-------|------|-----------|
| id | bigint | Primary Key |
| judul | string | Judul kegiatan |
| penanggung_jawab | string | Nama PJ |
| status_qc | enum | DRAFT, PENDING, APPROVED, REJECTED |
| catatan_terakhir | text | Ringkasan analisis AI |
| created_at | timestamp | Waktu dibuat |

### Tabel `dokumen`
| Kolom | Type | Deskripsi |
|-------|------|-----------|
| id | bigint | Primary Key |
| kegiatan_id | bigint | FK ke kegiatan |
| jenis_dokumen | enum | PROPOSAL, LAPORAN, BUKTI |
| file_path | string | Path file di storage |
| file_name | string | Nama asli file |
| isi_teks_extracted | longtext | Teks hasil ekstraksi PDF |
| created_at | timestamp | Waktu upload |

### Tabel `audit_logs`
| Kolom | Type | Deskripsi |
|-------|------|-----------|
| id | bigint | Primary Key |
| kegiatan_id | bigint | FK ke kegiatan |
| hasil_status | enum | MATCH, MISMATCH |
| analisa_ai | longtext | Penjelasan lengkap dari AI |
| token_usage | integer | Jumlah token dipakai (untuk tracking biaya) |
| created_at | timestamp | Waktu analisis |

---

## 🔧 Troubleshooting

### Error: "Class 'Smalot\PdfParser\Parser' not found"

**Solusi:**
```bash
composer require smalot/pdfparser
```

---

### Error: "SQLSTATE[08006] Connection refused"

**PostgreSQL tidak running:**
```bash
# Windows - Buka Services
# Cari "postgresql-x64-XX"
# Klik "Start"

# Atau via CMD (sebagai Admin)
net start postgresql-x64-16
```

---

### Error: "Gemini API: Quota exceeded"

**Penyebab:** Free tier limit tercapai (15 req/min atau 1500 req/hari)

**Solusi:**
1. Tunggu 1 menit (untuk rate limit)
2. Atau tunggu besok (untuk daily limit)
3. Atau upgrade ke paid tier

---

### Error: "The POST method is not supported"

**Penyebab:** Route tidak match atau CSRF token expired

**Solusi:**
```bash
# Clear cache
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# Restart server
php artisan serve
```

---

### Upload PDF berhasil tapi tabel kosong

**Penyebab:** Transaction rollback karena error parsing PDF atau API

**Solusi:**
1. Cek log error:
   ```bash
   # Windows
   type storage\logs\laravel.log | more
   
   # Atau buka file: storage/logs/laravel.log
   ```

2. Cek apakah PDF valid (bukan scan gambar tanpa OCR)

3. Cek API key Gemini valid:
   ```bash
   php artisan tinker
   
   # Di tinker console:
   dd(env('GEMINI_API_KEY'));
   ```

---

## 📦 Dependencies yang Diinstall

### PHP Packages (Composer)
```json
{
  "laravel/framework": "^11.0",
  "smalot/pdfparser": "^2.0",
  "gemini-api/laravel": "^1.0"
}
```

### JavaScript Packages (NPM)
```json
{
  "@tailwindcss/browser": "^4",
  "flowbite": "^3.1.2",
  "vite": "^5.0"
}
```

---

## 🔐 Keamanan

### Jangan Share/Commit:
- `.env` file (sudah ada di `.gitignore`)
- `storage/logs/` (berisi log sensitif)
- Gemini API Key
- Database credentials

### File `.gitignore` harus include:
```
.env
storage/*.log
vendor/
node_modules/
```

---

## 🤝 Bantuan

Jika ada masalah:

1. **Cek log Laravel:**
   ```
   storage/logs/laravel.log
   ```

2. **Enable debug mode** (di `.env`):
   ```env
   APP_DEBUG=true
   ```

3. **Test Gemini API:**
   - Akses: http://127.0.0.1:8000/gemini-test
   - Harus return response dari AI

4. **Test Database:**
   ```bash
   php artisan tinker
   
   # Di console:
   \App\Models\Kegiatan::count()
   ```

---

## 📝 Catatan Tambahan

### Free Tier Gemini API
- Model: `gemini-2.5-flash` (tercepat & gratis)
- Limit: 1500 requests/hari
- Upgrade ke Pro: `gemini-2.5-pro` (lebih akurat tapi berbayar)

### Alternatif API (jika Gemini limit habis)
- OpenAI GPT-4 (berbayar, $0.03/1k tokens)
- Claude AI (berbayar, $0.015/1k tokens)
- Groq API (gratis untuk testing)

### Performance Tips
- Upload PDF max 10 halaman untuk hasil cepat
- File > 5MB akan lambat di-process
- Simpan hasil ekstraksi teks di DB agar tidak perlu extract ulang

---

## 📞 Contact

Jika ada pertanyaan atau butuh bantuan setup, hubungi lead developer tim.

---

**Selamat Mencoba! 🚀**

*Dokumentasi ini dibuat untuk memudahkan onboarding rekan tim baru.*

---

**Last Updated:** 9 Januari 2026
