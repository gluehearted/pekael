<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-6">Bandingkan Dokumen</h3>

                    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- File Upload 1 -->
                        <div>
                            <label for="file1" class="block text-sm font-medium mb-2">
                                Dokumen 1 (PDF/Gambar)
                            </label>
                            <input type="file" id="file1" name="file1" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-200"
                                onchange="previewFile('file1', 'preview1')" required>
                            <!-- Preview Area 1 -->
                            <div id="preview1" class="mt-4 hidden">
                                <p class="text-sm font-medium mb-2">Preview:</p>
                                <div
                                    class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                                    <img id="preview1-img" class="max-w-full h-auto rounded hidden" alt="Preview">
                                    <div id="preview1-pdf" class="hidden">
                                        <iframe id="preview1-pdf-frame" class="w-full h-96 rounded"></iframe>
                                    </div>
                                    <p id="preview1-name" class="text-sm text-gray-600 dark:text-gray-400 mt-2"></p>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload 2 -->
                        <div>
                            <label for="file2" class="block text-sm font-medium mb-2">
                                Dokumen 2 (PDF/Gambar)
                            </label>
                            <input type="file" id="file2" name="file2" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-200"
                                onchange="previewFile('file2', 'preview2')" required>
                            <!-- Preview Area 2 -->
                            <div id="preview2" class="mt-4 hidden">
                                <p class="text-sm font-medium mb-2">Preview:</p>
                                <div
                                    class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                                    <img id="preview2-img" class="max-w-full h-auto rounded hidden" alt="Preview">
                                    <div id="preview2-pdf" class="hidden">
                                        <iframe id="preview2-pdf-frame" class="w-full h-96 rounded"></iframe>
                                    </div>
                                    <p id="preview2-name" class="text-sm text-gray-600 dark:text-gray-400 mt-2"></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                                Bandingkan Dokumen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
