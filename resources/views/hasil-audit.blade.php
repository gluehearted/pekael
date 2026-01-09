<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container">
            <h2>Hasil Audit: {{ $kegiatan->judul }}</h2>

            @if ($kegiatan->status_qc == 'APPROVED')
                <span class="badge bg-success fs-5">✅ APPROVED (Cocok)</span>
            @else
                <span class="badge bg-danger fs-5">❌ REJECTED (Tidak Cocok)</span>
            @endif

            <hr>

            <div class="card shadow-sm mt-4">
                <div class="card-header bg-dark text-white">🤖 Analisa AI Gemini</div>
                <div class="card-body text-white">
                    {!! \Illuminate\Support\Str::markdown($kegiatan->catatan_terakhir) !!}
                </div>
            </div>

            <a href="{{ route('kegiatan.create') }}" class="btn btn-secondary mt-3">⬅ Kembali Upload Baru</a>
        </div>
    </div>

    <script>
        function previewFile(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const previewImg = document.getElementById(previewId + '-img');
            const previewPdf = document.getElementById(previewId + '-pdf');
            const previewPdfFrame = document.getElementById(previewId + '-pdf-frame');
            const previewName = document.getElementById(previewId + '-name');

            const file = input.files[0];

            if (file) {
                preview.classList.remove('hidden');
                previewName.textContent = `File: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;

                const fileType = file.type;

                if (fileType.startsWith('image/')) {
                    // Show image preview
                    previewImg.classList.remove('hidden');
                    previewPdf.classList.add('hidden');

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else if (fileType === 'application/pdf') {
                    // Show PDF preview
                    previewImg.classList.add('hidden');
                    previewPdf.classList.remove('hidden');

                    const fileURL = URL.createObjectURL(file);
                    previewPdfFrame.src = fileURL;
                }
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
