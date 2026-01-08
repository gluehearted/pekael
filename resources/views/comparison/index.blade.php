<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document QC Comparison</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <h1 class="text-2xl font-bold text-gray-900">QC Document Comparison</h1>
                <p class="text-sm text-gray-600">Bandingkan Laporan dengan Proposal menggunakan AI</p>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Upload Form -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Upload Dokumen</h2>

                <form action="{{ route('comparison.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <!-- Report Upload -->
                        <div>
                            <label for="report" class="block mb-2 text-sm font-medium text-gray-900">
                                Laporan (Report) <span class="text-red-500">*</span>
                            </label>
                            <input type="file" id="report" name="report"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" required
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2.5">
                            <p class="mt-1 text-xs text-gray-500">PDF, DOCX, Excel, atau Gambar (max 10MB)</p>
                            @error('report')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Proposal Upload -->
                        <div>
                            <label for="proposal" class="block mb-2 text-sm font-medium text-gray-900">
                                Proposal <span class="text-red-500">*</span>
                            </label>
                            <input type="file" id="proposal" name="proposal"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" required
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2.5">
                            <p class="mt-1 text-xs text-gray-500">PDF, DOCX, Excel, atau Gambar (max 10MB)</p>
                            @error('proposal')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full md:w-auto text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-8 py-3">
                        Mulai Analisis QC
                    </button>
                </form>
            </div>

            <!-- History Table -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Riwayat Comparison</h2>

                @if ($comparisons->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-700">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Laporan</th>
                                    <th class="px-4 py-3">Proposal</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Score</th>
                                    <th class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($comparisons as $comparison)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ $comparison->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-3">{{ $comparison->report_filename }}</td>
                                        <td class="px-4 py-3">{{ $comparison->proposal_filename }}</td>
                                        <td class="px-4 py-3">
                                            @if ($comparison->status === 'completed')
                                                <span
                                                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Selesai</span>
                                            @elseif($comparison->status === 'processing')
                                                <span
                                                    class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Proses</span>
                                            @elseif($comparison->status === 'failed')
                                                <span
                                                    class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Gagal</span>
                                            @else
                                                <span
                                                    class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $comparison->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($comparison->overall_score)
                                                <span
                                                    class="font-semibold {{ $comparison->overall_score >= 80 ? 'text-green-600' : ($comparison->overall_score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                                    {{ number_format($comparison->overall_score, 1) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('comparison.show', $comparison->id) }}"
                                                class="text-blue-600 hover:underline">
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $comparisons->links() }}
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Belum ada riwayat comparison. Upload dokumen untuk
                        memulai.</p>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
