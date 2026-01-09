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
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('penanggung_jawab');

            // Status QC: DRAFT (Belum dicek), PENDING (Sedang dicek), APPROVED, REJECTED
            $table->enum('status_qc', ['DRAFT', 'PENDING', 'APPROVED', 'REJECTED'])->default('DRAFT');

            // Simpan ringkasan AI terakhir di sini biar gampang ditampilkan di tabel dashboard
            $table->text('catatan_terakhir')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
