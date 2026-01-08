<?php

namespace App\Services;

use Gemini;

class GeminiService
{
    private $client;
    private $model = 'gemini-1.5-flash'; // Free tier, bisa ganti ke gemini-1.5-pro untuk kualitas lebih baik

    public function __construct()
    {
        $apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            throw new \Exception('Gemini API key tidak ditemukan di .env');
        }

        $this->client = Gemini::client($apiKey);
    }

    /**
     * Compare dua text section dan return analysis
     */
    public function compareTexts(string $reportText, string $proposalText): array
    {
        $prompt = $this->buildComparisonPrompt($reportText, $proposalText);

        try {
            $response = $this->client->geminiPro()->generateContent($prompt);

            // Parse response JSON dari Gemini
            $text = $response->text();

            // Extract JSON dari response (kadang Gemini wrap dengan markdown)
            if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
                $jsonText = $matches[1];
            } else {
                $jsonText = $text;
            }

            $result = json_decode($jsonText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Gagal parse JSON response dari Gemini: ' . json_last_error_msg());
            }

            return $result;
        } catch (\Exception $e) {
            throw new \Exception('Gagal memanggil Gemini API: ' . $e->getMessage());
        }
    }

    /**
     * Build prompt untuk comparison
     */
    private function buildComparisonPrompt(string $reportText, string $proposalText): string
    {
        return <<<PROMPT
Kamu adalah Quality Control Analyst yang ahli dalam membandingkan dokumen laporan dengan proposal.

**TUGAS:**
Bandingkan teks LAPORAN dengan teks PROPOSAL di bawah ini. Analisis kesesuaian, temukan perbedaan, dan identifikasi masalah.

**LAPORAN:**
```
{$reportText}
```

**PROPOSAL:**
```
{$proposalText}
```

**INSTRUKSI:**
1. Hitung similarity score (0-100) antara kedua teks
2. Identifikasi perbedaan/mismatch yang signifikan
3. Tentukan severity: critical (data penting berbeda/hilang), high (perbedaan substansial), medium (perbedaan minor), low (perbedaan tidak material)
4. Berikan deskripsi masalah yang jelas dan spesifik
5. Berikan saran perbaikan (jika ada)

**OUTPUT FORMAT (JSON ONLY, NO EXPLANATION):**
```json
{
  "similarity_score": 85.5,
  "is_match": true,
  "severity": "medium",
  "issue_description": "Deskripsi singkat masalah yang ditemukan",
  "differences": [
    "Perbedaan 1: ...",
    "Perbedaan 2: ..."
  ],
  "suggestion": "Saran perbaikan untuk laporan"
}
```

**PENTING:**
- Jika tidak ada proposal text (kosong/null), set severity="critical" dan issue_description="Tidak ada data pembanding di proposal"
- Jika similarity_score >= 80, set is_match=true
- Berikan output HANYA dalam format JSON, tanpa penjelasan tambahan
PROMPT;
    }

    /**
     * Generate summary dari semua mismatches
     */
    public function generateSummary(array $mismatches): string
    {
        if (empty($mismatches)) {
            return 'Tidak ada perbedaan signifikan ditemukan. Dokumen laporan sesuai dengan proposal.';
        }

        $mismatchTexts = array_map(function ($mismatch) {
            return sprintf(
                "- [%s] %s (Score: %.1f%%)",
                strtoupper($mismatch['severity']),
                $mismatch['issue_description'],
                $mismatch['similarity_score']
            );
        }, $mismatches);

        $prompt = <<<PROMPT
Kamu adalah QC Analyst. Buatkan executive summary dari temuan berikut:

**TEMUAN:**
{$this->formatMismatchesForSummary($mismatches)}

**INSTRUKSI:**
1. Buat ringkasan singkat (max 200 kata) dalam Bahasa Indonesia
2. Highlight temuan critical/high priority
3. Berikan rekomendasi umum untuk perbaikan
4. Gunakan tone profesional

**OUTPUT:** (plaintext, bukan JSON)
PROMPT;

        try {
            $response = $this->client->geminiPro()->generateContent($prompt);
            return trim($response->text());
        } catch (\Exception $e) {
            // Fallback jika API gagal
            $criticalCount = count(array_filter($mismatches, fn($m) => $m['severity'] === 'critical'));
            $highCount = count(array_filter($mismatches, fn($m) => $m['severity'] === 'high'));

            return sprintf(
                "Ditemukan %d perbedaan: %d critical, %d high priority. Perlu review lebih lanjut.",
                count($mismatches),
                $criticalCount,
                $highCount
            );
        }
    }

    /**
     * Format mismatches untuk summary prompt
     */
    private function formatMismatchesForSummary(array $mismatches): string
    {
        return implode("\n", array_map(function ($mismatch, $index) {
            return sprintf(
                "%d. [%s] Section: %s\n   Issue: %s\n   Score: %.1f%%",
                $index + 1,
                strtoupper($mismatch['severity']),
                $mismatch['section'] ?? 'N/A',
                $mismatch['issue_description'],
                $mismatch['similarity_score']
            );
        }, $mismatches, array_keys($mismatches)));
    }
}
