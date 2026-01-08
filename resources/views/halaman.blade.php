<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

</head>

<body>
    <div class="h-screen p-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Upload File</h1>

            <form action="#" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- File Input 1 -->
                <div>
                    <label for="file1" class="block mb-2 text-sm font-medium text-gray-900">
                        File 1 (PDF atau Gambar)
                    </label>
                    <input type="file" id="file1" name="file1" accept=".pdf,.jpg,.jpeg,.png,.gif"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2.5">
                    <p class="mt-1 text-sm text-gray-500">PDF, JPG, JPEG, PNG, atau GIF</p>
                </div>

                <!-- File Input 2 -->
                <div>
                    <label for="file2" class="block mb-2 text-sm font-medium text-gray-900">
                        File 2 (PDF atau Gambar)
                    </label>
                    <input type="file" id="file2" name="file2" accept=".pdf,.jpg,.jpeg,.png,.gif"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2.5">
                    <p class="mt-1 text-sm text-gray-500">PDF, JPG, JPEG, PNG, atau GIF</p>
                </div>

                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Upload File
                </button>
            </form>
        </div>
    </div>
</body>

</html>
