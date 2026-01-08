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
        Schema::create('comparison_mismatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comparison_id')->constrained('document_comparisons')->onDelete('cascade');
            $table->string('section')->nullable();
            $table->text('report_text');
            $table->text('proposal_text')->nullable();
            $table->decimal('similarity_score', 5, 2);
            $table->text('issue_description');
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->json('ai_suggestion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparison_mismatches');
    }
};
