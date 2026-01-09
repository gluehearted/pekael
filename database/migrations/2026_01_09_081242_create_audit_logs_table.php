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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->onDelete('cascade');

            // Hasil dari AI (JSON mentah & Analisa HTML)
            $table->enum('hasil_status', ['MATCH', 'MISMATCH']); // Kesimpulan AI
            $table->longText('analisa_ai'); // Penjelasan panjang dari AI (HTML)

            // Opsional: Mencatat berapa token yang dipakai (untuk laporan biaya ke atasan)
            $table->integer('token_usage')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
