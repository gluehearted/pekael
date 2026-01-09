<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\Dokumen;
use App\Models\AuditLog;
use App\Services\AuditService; // Panggil Service Koki Kita
use Illuminate\Support\Facades\DB;

class KegiatanController extends Controller
{
    // 1. Tampilkan Form Upload
    public function create()
    {
        return view('upload-kegiatan');
    }

    // 2. Proses Simpan Data & File (Dibantu AuditService)
    public function store(Request $request, AuditService $auditService)
    {
        $request->validate([
            'judul' => 'required',
            'file_proposal' => 'required|mimes:pdf',
            'file_laporan' => 'required|mimes:pdf',
        ]);

        DB::beginTransaction();
        try {
            // A. Simpan Kegiatan
            $kegiatan = Kegiatan::create([
                'judul' => $request->judul,
                'penanggung_jawab' => 'Mahasiswa PKL',
                'status_qc' => 'PENDING'
            ]);

            // B. Proses Proposal (Panggil Service)
            $fileProposal = $request->file('file_proposal');
            $pathProposal = $fileProposal->store('dokumen_upload');
            $teksProposal = $auditService->ekstrakTeks($fileProposal, $pathProposal);

            Dokumen::create([
                'kegiatan_id'   => $kegiatan->id,
                'jenis_dokumen' => 'PROPOSAL',
                'file_path'     => $pathProposal,
                'file_name'     => $fileProposal->getClientOriginalName(),
                'isi_teks_extracted' => $teksProposal
            ]);

            // C. Proses Laporan (Panggil Service)
            $fileLaporan = $request->file('file_laporan');
            $pathLaporan = $fileLaporan->store('dokumen_upload');
            $teksLaporan = $auditService->ekstrakTeks($fileLaporan, $pathLaporan);

            Dokumen::create([
                'kegiatan_id'   => $kegiatan->id,
                'jenis_dokumen' => 'LAPORAN',
                'file_path'     => $pathLaporan,
                'file_name'     => $fileLaporan->getClientOriginalName(),
                'isi_teks_extracted' => $teksLaporan
            ]);

            DB::commit();

            // Redirect ke fungsi checkAi di bawah
            return redirect()->route('kegiatan.check', $kegiatan->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal upload: ' . $e->getMessage());
        }
    }

    // 3. LOGIKA AI CHECKING (INI YANG TADI HILANG)
    public function checkAi($id, AuditService $auditService)
    {
        $kegiatan = Kegiatan::with('dokumens')->findOrFail($id);

        // Ambil teks dari database (tidak perlu baca PDF lagi)
        $proposal = $kegiatan->dokumens->where('jenis_dokumen', 'PROPOSAL')->first()->isi_teks_extracted ?? '';
        $laporan  = $kegiatan->dokumens->where('jenis_dokumen', 'LAPORAN')->first()->isi_teks_extracted ?? '';

        // Panggil Koki AI (Service) buat mikir
        $hasilAI = $auditService->analisaDenganAI($proposal, $laporan);

        // Tentukan Status (Logic Sederhana)
        $statusAkhir = str_contains($hasilAI, 'MISMATCH') ? 'REJECTED' : 'APPROVED';

        // Simpan ke Log Audit
        AuditLog::create([
            'kegiatan_id' => $kegiatan->id,
            'hasil_status' => str_contains($hasilAI, 'MISMATCH') ? 'MISMATCH' : 'MATCH',
            'analisa_ai' => $hasilAI
        ]);

        // Update Status Kegiatan
        $kegiatan->update([
            'status_qc' => $statusAkhir,
            'catatan_terakhir' => $hasilAI
        ]);

        return redirect()->route('kegiatan.show', $kegiatan->id);
    }

    // 4. Halaman Hasil
    public function show($id)
    {
        $kegiatan = Kegiatan::with('auditLogs')->findOrFail($id);
        return view('hasil-audit', compact('kegiatan'));
    }
}
