<?php

require_once __DIR__ . '/../../Controllers/AuthController.php';

$auth = new AuthController();

$result = $auth->register();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Inventaris Barang</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100">

    <div class="flex min-h-screen">

        <!-- Left -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-green-600 to-emerald-700 items-center justify-center">

            <div class="text-center px-12 text-white">

                <h1 class="text-5xl font-bold mb-6">
                    Sistem Inventaris Barang
                </h1>

                <p class="text-lg text-green-100 leading-relaxed">
                    Buat akun untuk mulai mengelola data inventaris barang dengan mudah dan aman.
                </p>

            </div>

        </div>

        <!-- Right -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6">

            <div class="w-full max-w-md">

                <div class="bg-white rounded-2xl shadow-xl p-8">

                    <div class="text-center mb-8">

                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-10 h-10 text-green-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M18 9v6M15 12h6M12 5a4 4 0 100 8 4 4 0 000-8zm-7 14a7 7 0 1114 0H5z" />

                            </svg>

                        </div>

                        <h2 class="text-3xl font-bold text-gray-800">
                            Registrasi
                        </h2>

                        <p class="text-gray-500 mt-2">
                            Silakan lengkapi data di bawah ini.
                        </p>

                    </div>
                    <?php if ($result): ?>

                        <div class="mb-4">

                            <div class="<?= $result['status']
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-700'; ?> p-3 rounded">

                                <?= $result['message']; ?>

                            </div>

                        </div>

                    <?php endif; ?>
                    <form action="" method="POST" class="space-y-5">

                        <!-- Nama Lengkap -->
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                name="nama_lengkap"
                                placeholder="Masukkan nama lengkap"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">

                        </div>

                        <!-- Username -->
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                placeholder="Masukkan username"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">

                        </div>

                        <!-- Password -->
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                placeholder="Masukkan password"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">

                        </div>

                        <!-- Konfirmasi Password -->
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password
                            </label>

                            <input
                                type="password"
                                name="konfirmasi_password"
                                placeholder="Ulangi password"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">

                        </div>

                        <button
                            type="submit"
                            name="register"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition">

                            Daftar Sekarang

                        </button>

                        <div class="text-center">

                            <p class="text-sm text-gray-600">

                                Sudah punya akun?

                                <a href="login.php"
                                    class="text-green-600 font-semibold hover:underline">
                                    Login
                                </a>

                            </p>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</body>

</html>