<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Spatie\PdfToText\Pdf;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as ExcelIOFactory;
use thiagoalessio\TesseractOCR\TesseractOCR;

class DocumentExtractorService
{
    /**
     * Extract text dari berbagai format dokumen
     */
    public function extractText(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'pdf' => $this->extractFromPdf($file),
            'doc', 'docx' => $this->extractFromWord($file),
            'xls', 'xlsx' => $this->extractFromExcel($file),
            'jpg', 'jpeg', 'png', 'gif' => $this->extractFromImage($file),
            default => throw new \Exception("Format file tidak didukung: {$extension}"),
        };
    }

    /**
     * Extract text dari PDF
     */
    private function extractFromPdf(UploadedFile $file): string
    {
        try {
            $pdfPath = $file->getRealPath();

            // Gunakan pdftotext (harus install poppler-utils)
            $text = (new Pdf())
                ->setPdf($pdfPath)
                ->text();

            return $this->cleanText($text);
        } catch (\Exception $e) {
            throw new \Exception("Gagal extract PDF: " . $e->getMessage());
        }
    }

    /**
     * Extract text dari Word (DOC/DOCX)
     */
    private function extractFromWord(UploadedFile $file): string
    {
        try {
            $phpWord = WordIOFactory::load($file->getRealPath());
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $text .= $childElement->getText() . "\n";
                            }
                        }
                    }
                }
            }

            return $this->cleanText($text);
        } catch (\Exception $e) {
            throw new \Exception("Gagal extract Word: " . $e->getMessage());
        }
    }

    /**
     * Extract text dari Excel
     */
    private function extractFromExcel(UploadedFile $file): string
    {
        try {
            $spreadsheet = ExcelIOFactory::load($file->getRealPath());
            $text = '';

            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $text .= "Sheet: " . $sheet->getTitle() . "\n";

                foreach ($sheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    $rowData = [];
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }

                    $text .= implode("\t", $rowData) . "\n";
                }

                $text .= "\n";
            }

            return $this->cleanText($text);
        } catch (\Exception $e) {
            throw new \Exception("Gagal extract Excel: " . $e->getMessage());
        }
    }

    /**
     * Extract text dari gambar menggunakan OCR
     */
    private function extractFromImage(UploadedFile $file): string
    {
        try {
            // Simpan temporary file
            $tempPath = $file->getRealPath();

            // Gunakan Tesseract OCR
            $text = (new TesseractOCR($tempPath))
                ->lang('ind', 'eng') // Support Indonesian & English
                ->run();

            return $this->cleanText($text);
        } catch (\Exception $e) {
            throw new \Exception("Gagal extract gambar (OCR): " . $e->getMessage());
        }
    }

    /**
     * Clean dan normalize text
     */
    private function cleanText(string $text): string
    {
        // Remove extra whitespaces
        $text = preg_replace('/\s+/', ' ', $text);

        // Remove special characters tapi keep punctuation
        $text = preg_replace('/[^\p{L}\p{N}\s\.\,\;\:\-\(\)]/u', '', $text);

        // Trim
        $text = trim($text);

        return $text;
    }

    /**
     * Split text ke paragraf atau section
     */
    public function splitToSections(string $text, int $maxChunkSize = 1500): array
    {
        // Split by double newline atau paragraf
        $paragraphs = preg_split('/\n\s*\n/', $text);

        $sections = [];
        $currentSection = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if (empty($paragraph)) {
                continue;
            }

            // Jika menambah paragraph melebihi max size, simpan section sekarang
            if (strlen($currentSection . ' ' . $paragraph) > $maxChunkSize) {
                if (!empty($currentSection)) {
                    $sections[] = $currentSection;
                }
                $currentSection = $paragraph;
            } else {
                $currentSection .= ($currentSection ? "\n\n" : '') . $paragraph;
            }
        }

        // Simpan section terakhir
        if (!empty($currentSection)) {
            $sections[] = $currentSection;
        }

        return $sections;
    }
}
