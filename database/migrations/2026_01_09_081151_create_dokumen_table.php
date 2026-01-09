<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel kegiatan (Jika kegiatan dihapus, dokumen ikut terhapus)
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->onDelete('cascade');

            // Jenis file: Proposal (Rencana), Laporan (Realisasi), Bukti (Gambar)
            $table->enum('jenis_dokumen', ['PROPOSAL', 'LAPORAN', 'BUKTI']);

            $table->string('file_path'); // Lokasi file di storage (misal: public/uploads/file.pdf)
            $table->string('file_name'); // Nama asli file

            // FITUR HEMAT BIAYA:
            // Kita simpan teks hasil bacaan PDF/OCR di sini.
            // Jadi kalau mau cek ulang, gak perlu bayar API buat baca lagi. Cukup ambil dari database.
            $table->longText('isi_teks_extracted')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
