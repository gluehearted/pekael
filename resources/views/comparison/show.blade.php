<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Comparison - QC Document</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detail Comparison</h1>
                        <p class="text-sm text-gray-600">Hasil analisis QC dokumen</p>
                    </div>
                    <a href="{{ route('comparison.index') }}"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        ← Kembali
                    </a>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Summary Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Laporan</h3>
                        <p class="text-lg font-semibold">{{ $comparison->report_filename }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Proposal</h3>
                        <p class="text-lg font-semibold">{{ $comparison->proposal_filename }}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Status</p>
                        <p class="text-lg font-semibold">
                            @if ($comparison->status === 'completed')
                                <span class="text-green-600">✓ Selesai</span>
                            @elseif($comparison->status === 'processing')
                                <span class="text-yellow-600">⏳ Proses</span>
                            @elseif($comparison->status === 'failed')
                                <span class="text-red-600">✗ Gagal</span>
                            @endif
                        </p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Overall Score</p>
                        <p
                            class="text-2xl font-bold {{ $comparison->overall_score >= 80 ? 'text-green-600' : ($comparison->overall_score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $comparison->overall_score ? number_format($comparison->overall_score, 1) . '%' : '-' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Jumlah Section</p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $comparison->metadata['report_sections'] ?? '-' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Mismatches</p>
                        <p class="text-2xl font-bold text-red-600">
                            {{ $comparison->mismatches->count() }}
                        </p>
                    </div>
                </div>

                @if ($comparison->summary)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Executive Summary</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $comparison->summary }}</p>
                    </div>
                @endif
            </div>

            <!-- Mismatches Detail -->
            @if ($comparison->mismatches->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Detail Temuan Mismatch</h2>

                    <div class="space-y-4">
                        @foreach ($comparison->mismatches as $mismatch)
                            <div
                                class="border rounded-lg p-4 {{ $mismatch->severity === 'critical' ? 'border-red-300 bg-red-50' : ($mismatch->severity === 'high' ? 'border-orange-300 bg-orange-50' : 'border-yellow-300 bg-yellow-50') }}">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <span
                                            class="text-xs font-semibold px-2 py-1 rounded {{ $mismatch->severity === 'critical' ? 'bg-red-200 text-red-800' : ($mismatch->severity === 'high' ? 'bg-orange-200 text-orange-800' : 'bg-yellow-200 text-yellow-800') }}">
                                            {{ strtoupper($mismatch->severity) }}
                                        </span>
                                        <span
                                            class="ml-2 text-sm font-medium text-gray-700">{{ $mismatch->section }}</span>
                                    </div>
                                    <span
                                        class="text-lg font-bold {{ $mismatch->similarity_score >= 60 ? 'text-yellow-600' : 'text-red-600' }}">
                                        {{ number_format($mismatch->similarity_score, 1) }}%
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700 mb-1">Issue:</p>
                                    <p class="text-sm text-gray-800">{{ $mismatch->issue_description }}</p>
                                </div>

                                <div class="grid md:grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 mb-1">Teks di Laporan:</p>
                                        <div
                                            class="bg-white p-2 rounded text-xs text-gray-700 max-h-32 overflow-y-auto">
                                            {{ $mismatch->report_text }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 mb-1">Teks di Proposal:</p>
                                        <div
                                            class="bg-white p-2 rounded text-xs text-gray-700 max-h-32 overflow-y-auto">
                                            {{ $mismatch->proposal_text ?: '(Tidak ada data pembanding)' }}
                                        </div>
                                    </div>
                                </div>

                                @if ($mismatch->ai_suggestion && isset($mismatch->ai_suggestion['suggestion']))
                                    <div class="bg-white p-3 rounded">
                                        <p class="text-xs font-semibold text-green-700 mb-1">💡 AI Suggestion:</p>
                                        <p class="text-xs text-gray-700">{{ $mismatch->ai_suggestion['suggestion'] }}
                                        </p>
                                    </div>
                                @endif

                                @if ($mismatch->ai_suggestion && isset($mismatch->ai_suggestion['differences']))
                                    <details class="mt-2">
                                        <summary class="text-xs font-semibold text-gray-600 cursor-pointer">Lihat
                                            Perbedaan Detail</summary>
                                        <ul class="mt-2 ml-4 text-xs text-gray-700 list-disc">
                                            @foreach ($mismatch->ai_suggestion['differences'] as $diff)
                                                <li>{{ $diff }}</li>
                                            @endforeach
                                        </ul>
                                    </details>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <p class="text-green-600 font-semibold text-lg">✓ Tidak ada mismatch ditemukan!</p>
                    <p class="text-gray-600 text-sm mt-2">Dokumen laporan sudah sesuai dengan proposal.</p>
                </div>
            @endif

        </div>
    </div>
</body>

</html>
