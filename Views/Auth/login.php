<?php

session_start();

require_once __DIR__ . '/../../Controllers/AuthController.php';

$auth = new AuthController();

$result = $auth->login();

if (
    !isset($_SESSION['login']) &&
    isset($_COOKIE['remember_login'])
) {

    $userModel = new User();

    $user = $userModel->getUserById($_COOKIE['remember_login']);

    if ($user) {

        $_SESSION['login'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris Barang</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100">

    <div class="flex min-h-screen">

        <!-- Left -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 to-indigo-700 items-center justify-center">

            <div class="text-center px-12 text-white">

                <h1 class="text-5xl font-bold mb-6">
                    Sistem Inventaris Barang
                </h1>

                <p class="text-lg text-blue-100 leading-relaxed">
                    Kelola data barang, supplier, transaksi barang masuk, dan
                    barang keluar.
                </p>

            </div>

        </div>

        <!-- Right -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6">

            <div class="w-full max-w-md">

                <div class="bg-white rounded-2xl shadow-xl p-8">

                    <div class="text-center mb-8">

                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-10 h-10 text-blue-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5.121 17.804A9 9 0 1118.364 4.56M15 11a3 3 0 11-6 0 3 3 0 016 0zm-9 8a9 9 0 0118 0" />

                            </svg>

                        </div>

                        <h2 class="text-3xl font-bold text-gray-800">
                            Login
                        </h2>

                        <p class="text-gray-500 mt-2">
                            Silakan masuk ke akun Anda
                        </p>

                    </div>
                    <?php if (isset($_SESSION['success'])) : ?>

                        <div class="mb-4 rounded-lg bg-green-100 border border-green-300 p-4 text-green-700">
                            <?= $_SESSION['success']; ?>
                        </div>

                        <?php unset($_SESSION['success']); ?>

                    <?php endif; ?>
                    <?php if (isset($result) && !$result['status']) : ?>

                        <div class="mb-4 rounded-lg bg-red-100 border border-red-300 p-4 text-red-700">

                            <?= $result['message']; ?>

                        </div>

                    <?php endif; ?>
                    <form action="" method="POST" class="space-y-5">

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                placeholder="Masukkan username"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">

                        </div>

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                placeholder="Masukkan password"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">

                        </div>

                        <div class="flex items-center justify-between">

                            <label class="flex items-center gap-2">

                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="rounded text-blue-600">

                                <span class="text-sm text-gray-600">
                                    Remember Me
                                </span>

                            </label>

                            <a href="./registrasi.php"
                                class="text-sm text-blue-600 hover:underline">
                                Registrasi
                            </a>

                        </div>

                        <button
                            type="submit"
                            name="login"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">

                            Login

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</body>

</html>