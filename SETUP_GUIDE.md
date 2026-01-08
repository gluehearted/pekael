# Document QC Comparison System

Sistem Quality Control untuk membandingkan dokumen Laporan dengan Proposal menggunakan AI (Google Gemini).

## Fitur

- ✅ Upload dokumen: PDF, DOCX, Excel, Gambar (JPG/PNG)
- ✅ Ekstraksi teks otomatis dengan OCR untuk gambar
- ✅ AI-powered comparison menggunakan Gemini API
- ✅ Scoring & severity classification (critical/high/medium/low)
- ✅ Executive summary & detail mismatch
- ✅ Riwayat comparison tersimpan di database
- ✅ Dashboard untuk tracking hasil QC

## Instalasi

### 1. Install Dependencies

```bash
composer install
```

### 2. Setup Environment

Copy `.env.example` ke `.env`:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

### 3. Dapatkan Gemini API Key

1. Buka [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Sign in dengan akun Google
3. Klik "Get API Key" atau "Create API Key"
4. Copy API key yang digenerate
5. Paste ke file `.env`:

```env
GEMINI_API_KEY=your-api-key-here
```

**Free Tier Limits:**
- 15 requests per minute
- 1500 requests per day
- Gratis selamanya untuk Gemini 1.5 Flash

### 4. Setup Database

Sesuaikan konfigurasi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pkl1
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration:
```bash
php artisan migrate
```

### 5. Install Tools Ekstraksi (Optional)

**Untuk PDF:** Install [poppler-utils](https://blog.alivate.com.au/poppler-windows/)
- Download & extract ke `C:\Program Files\poppler`
- Tambahkan `C:\Program Files\poppler\Library\bin` ke PATH

**Untuk OCR Gambar:** Install [Tesseract OCR](https://github.com/UB-Mannheim/tesseract/wiki)
- Download installer untuk Windows
- Install dengan bahasa Indonesia & English
- Tesseract akan otomatis terdeteksi

## Cara Menggunakan

### 1. Jalankan Server

```bash
php artisan serve
```

Buka browser: http://127.0.0.1:8000/comparison

### 2. Upload Dokumen

1. Pilih file **Laporan** (dokumen yang ingin di-QC)
2. Pilih file **Proposal** (dokumen pembanding/referensi)
3. Klik **"Mulai Analisis QC"**

### 3. Lihat Hasil

Sistem akan:
- Extract teks dari kedua dokumen
- Membandingkan section demi section
- Memberikan similarity score (0-100%)
- Mendeteksi mismatch & perbedaan
- Generate executive summary
- Simpan hasil ke database

### 4. Interpretasi Hasil

**Overall Score:**
- ✅ ≥ 80%: Dokumen sangat sesuai
- ⚠️ 60-79%: Ada beberapa perbedaan
- ❌ < 60%: Banyak perbedaan signifikan

**Severity:**
- 🔴 **Critical**: Data penting berbeda/hilang
- 🟠 **High**: Perbedaan substansial
- 🟡 **Medium**: Perbedaan minor
- 🟢 **Low**: Perbedaan tidak material

## Struktur File Penting

```
app/
├── Http/Controllers/
│   └── DocumentComparisonController.php   # Main controller
├── Models/
│   ├── DocumentComparison.php            # Model untuk comparison
│   └── ComparisonMismatch.php            # Model untuk mismatch
└── Services/
    ├── DocumentExtractorService.php      # Ekstraksi teks dari file
    └── GeminiService.php                 # Integrasi Gemini API

resources/views/comparison/
├── index.blade.php                       # Upload form & history
└── show.blade.php                        # Detail hasil comparison

database/migrations/
├── *_create_document_comparisons_table.php
└── *_create_comparison_mismatches_table.php
```

## Troubleshooting

### Error: "Gemini API key tidak ditemukan"
- Pastikan `GEMINI_API_KEY` sudah diset di `.env`
- Restart server setelah update `.env`

### Error: "Gagal extract PDF"
- Install poppler-utils (lihat bagian instalasi)
- Pastikan PDF tidak ter-password

### Error: "Gagal extract gambar (OCR)"
- Install Tesseract OCR
- Pastikan gambar cukup jelas (tidak blur)

### Proses terlalu lama
- Dokumen besar akan butuh waktu lebih lama
- Consider implementasi Queue/Job untuk async processing
- Gemini free tier ada rate limit (15 req/min)

## Optimisasi (Optional)

### 1. Async Processing dengan Queue

Uncomment di `DocumentComparisonController`:
```php
// Dispatch ke queue instead of processComparison()
ProcessComparisonJob::dispatch($comparison);
```

### 2. Upgrade ke Gemini Pro

Edit `GeminiService.php`:
```php
private $model = 'gemini-1.5-pro'; // Lebih akurat tapi slower
```

### 3. Caching untuk Document Yang Sama

Implementasi caching di `DocumentExtractorService` untuk avoid re-extract file yang sama.

## Support

- Google Gemini API: https://ai.google.dev/docs
- Laravel Docs: https://laravel.com/docs
- Issues: Hubungi developer

## License

MIT License
