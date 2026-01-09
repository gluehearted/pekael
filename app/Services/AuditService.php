<?php

namespace App\Services;

use App\Models\Dokumen;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Storage;

class AuditService
{
    public function ekstrakTeks($file, $path)
    {
        $parser = new Parser();
        // Pastikan path storage benar
        $fullPath = Storage::path($path);

        $pdf = $parser->parseFile($fullPath);
        return $pdf->getText();
    }

    public function analisaDenganAI($proposalText, $laporanText)
    {
        $apiKey = env('GEMINI_API_KEY');

        // Prompt kita susun di sini biar rapi
        $prompt = "Bandingkan Dokumen Proposal berikut: \n " . substr($proposalText, 0, 5000) .
            "\n\n Dengan Dokumen Laporan berikut: \n " . substr($laporanText, 0, 5000) .
            "\n\n Kamu adalah Quality Control Analyst yang ahli dalam membandingkan dokumen laporan dengan proposal.
            **TUGAS:**
            Bandingkan teks LAPORAN dengan teks PROPOSAL di bawah ini. temukan perbedaan atau ketidaksesuaian antara keduanya.
            Fokus pada:
            1. Apakah semua poin yang dijanjikan di PROPOSAL telah direalisasikan di LAPORAN?
            2. Apakah ada informasi di LAPORAN yang tidak ada di PROPOSAL?
            **FORMAT OUTPUT:**
            Berikan kesimpulan singkat apakah LAPORAN SESUAI atau TIDAK SESUAI dengan PROPOSAL beserta alasannya.
            Jika ada ketidaksesuaian, jelaskan secara rinci bagian mana yang berbeda dan mengapa.
            Jawab dengan jujur dan transparan berdasarkan analisis kamu dan ketika proposal sudah ada semuanya di laporan maka itu bisa dikatakan MATCH namun jika ada yang kurang atau tidak sesuai maka itu adalah MISMATCH.";

        // Body Request LENGKAP (Jangan disingkat)
        /** @var Response $response */
        $response = Http::timeout(120) // <--- TAMBAHKAN INI (120 Detik / 2 Menit)
            ->retry(3, 100) // Opsional: Coba ulang 3x kalau gagal koneksi
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                "contents" => [
                    [
                        "parts" => [
                            [
                                "text" => $prompt
                            ]
                        ]
                    ]
                ]
            ]);

        // Cek error kalau API gagal
        if ($response->failed()) {
            return "Gagal menghubungi AI: " . $response->body();
        }

        // Ambil teks jawaban
        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'AI tidak memberikan jawaban.';
    }
}
