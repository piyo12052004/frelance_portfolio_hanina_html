<?php
// Set HTTP Status Code ke 404 agar dibaca benar oleh browser & SEO
http_response_code(404);

// Ambil config jika butuh BASE_URL
require_once __DIR__ . '/Helper/config.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-tr from-indigo-600 via-purple-600 to-blue-500 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden p-8 text-center space-y-6 border border-purple-300">
        
        <div class="text-sky-500 text-7xl animate-bounce mt-4">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <h1 class="text-6xl font-extrabold text-gray-800 tracking-tight">404</h1>
        
        <div class="space-y-2">
            <h2 class="text-2xl font-bold text-gray-700">Halaman Tidak Ditemukan</h2>
            <p class="text-gray-500 text-sm px-4">
                Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan ke alamat lain.
            </p>
        </div>

        <div class="pt-4">
            <a href="<?= BASE_URL; ?>/index.php" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-sky-500 hover:bg-sky-600 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition">
                <i class="fas fa-home"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>

</html>