<?php

namespace App\Http\Controllers;

use App\Models\DocumentComparison;
use App\Models\ComparisonMismatch;
use App\Services\DocumentExtractorService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentComparisonController extends Controller
{
    private $extractor;
    private $gemini;

    public function __construct(DocumentExtractorService $extractor, GeminiService $gemini)
    {
        $this->extractor = $extractor;
        $this->gemini = $gemini;
    }

    /**
     * Show upload form
     */
    public function index()
    {
        $comparisons = DocumentComparison::orderBy('created_at', 'desc')
            ->paginate(10);

        return view('comparison.index', compact('comparisons'));
    }

    /**
     * Upload dan mulai comparison
     */
    public function upload(Request $request)
    {
        $request->validate([
            'report' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'proposal' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        try {
            // Simpan file
            $reportPath = $request->file('report')->store('documents/reports', 'local');
            $proposalPath = $request->file('proposal')->store('documents/proposals', 'local');

            // Buat record comparison
            $comparison = DocumentComparison::create([
                'report_filename' => $request->file('report')->getClientOriginalName(),
                'report_path' => $reportPath,
                'proposal_filename' => $request->file('proposal')->getClientOriginalName(),
                'proposal_path' => $proposalPath,
                'status' => 'processing',
            ]);

            // Process immediately (bisa dipindah ke Job untuk async)
            $this->processComparison($comparison);

            return redirect()->route('comparison.show', $comparison->id)
                ->with('success', 'Dokumen berhasil diupload dan sedang diproses');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal upload dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Show comparison result
     */
    public function show($id)
    {
        $comparison = DocumentComparison::with('mismatches')->findOrFail($id);

        return view('comparison.show', compact('comparison'));
    }

    /**
     * Process comparison (ekstrak teks & AI analysis)
     */
    private function processComparison(DocumentComparison $comparison)
    {
        try {
            // Get file paths
            $reportFile = Storage::disk('local')->path($comparison->report_path);
            $proposalFile = Storage::disk('local')->path($comparison->proposal_path);

            // Extract text
            $reportText = $this->extractor->extractText(
                new \Illuminate\Http\UploadedFile($reportFile, $comparison->report_filename)
            );
            $proposalText = $this->extractor->extractText(
                new \Illuminate\Http\UploadedFile($proposalFile, $comparison->proposal_filename)
            );

            // Split ke sections
            $reportSections = $this->extractor->splitToSections($reportText);
            $proposalSections = $this->extractor->splitToSections($proposalText);

            // Compare setiap section laporan dengan semua section proposal
            $allMismatches = [];
            $totalScore = 0;
            $sectionCount = 0;

            foreach ($reportSections as $index => $reportSection) {
                // Cari section proposal yang paling mirip (simple: bandingkan dengan section di index yang sama)
                $proposalSection = $proposalSections[$index] ?? '';

                // Call Gemini API untuk compare
                $analysis = $this->gemini->compareTexts($reportSection, $proposalSection);

                // Simpan mismatch jika score rendah atau ada issue
                if (!$analysis['is_match'] || $analysis['similarity_score'] < 80) {
                    $mismatch = ComparisonMismatch::create([
                        'comparison_id' => $comparison->id,
                        'section' => 'Section ' . ($index + 1),
                        'report_text' => substr($reportSection, 0, 500), // Simpan max 500 char
                        'proposal_text' => substr($proposalSection, 0, 500),
                        'similarity_score' => $analysis['similarity_score'],
                        'issue_description' => $analysis['issue_description'],
                        'severity' => $analysis['severity'],
                        'ai_suggestion' => [
                            'differences' => $analysis['differences'] ?? [],
                            'suggestion' => $analysis['suggestion'] ?? null,
                        ],
                    ]);

                    $allMismatches[] = $mismatch->toArray();
                }

                $totalScore += $analysis['similarity_score'];
                $sectionCount++;
            }

            // Calculate overall score
            $overallScore = $sectionCount > 0 ? $totalScore / $sectionCount : 0;

            // Generate summary
            $summary = $this->gemini->generateSummary($allMismatches);

            // Update comparison
            $comparison->update([
                'status' => 'completed',
                'overall_score' => $overallScore,
                'summary' => $summary,
                'metadata' => [
                    'report_sections' => count($reportSections),
                    'proposal_sections' => count($proposalSections),
                    'mismatches_count' => count($allMismatches),
                    'processed_at' => now()->toDateTimeString(),
                ],
            ]);
        } catch (\Exception $e) {
            $comparison->update([
                'status' => 'failed',
                'summary' => 'Error: ' . $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
