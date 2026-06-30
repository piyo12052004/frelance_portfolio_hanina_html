<?php

require_once "../../Helper/function.php";

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: ../../index.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = register($_POST);

    if ($result['status']) {
        // Set notifikasi sukses sebelum pindah halaman
        set_flash('success', 'Registrasi Berhasil!', 'Akun Anda sudah terdaftar, silakan login.');
        header("Location: login.php");
        exit;
    }
    $errors = $result['errors'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gradient-to-tr from-indigo-600 via-purple-600 to-blue-500 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-purple-300">

        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 py-4 text-center text-white">
            <h2 class="text-2xl font-semibold tracking-wide">Registrasi Akun</h2>
        </div>

        <div class="p-6 space-y-5">
            <form method="POST" class="space-y-4">

                <div class="space-y-1.5">
                    <label class="block text-gray-700 text-sm font-medium">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nama_lengkap"
                        value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>"
                        class="w-full px-4 py-2.5 rounded-lg border <?= isset($errors['nama_lengkap']) ? 'border-red-500' : 'border-gray-300' ?>"
                        placeholder="Masukkan nama lengkap">

                    <?php if (isset($errors['nama_lengkap'])) : ?>
                        <p class="text-red-500 text-sm mt-1">
                            <?= $errors['nama_lengkap'] ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-gray-700 text-sm font-medium">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        class="w-full px-4 py-2.5 rounded-lg border <?= isset($errors['username']) ? 'border-red-500' : 'border-gray-300' ?>"
                        placeholder="Masukkan username">

                    <?php if (isset($errors['username'])) : ?>
                        <p class="text-red-500 text-sm mt-1">
                            <?= $errors['username'] ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-gray-700 text-sm font-medium">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        name="password"
                        class="w-full px-4 py-2.5 rounded-lg border <?= isset($errors['password']) ? 'border-red-500' : 'border-gray-300' ?>"
                        placeholder="Masukkan password">

                    <?php if (isset($errors['password'])) : ?>
                        <p class="text-red-500 text-sm mt-1">
                            <?= $errors['password'] ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-gray-700 text-sm font-medium">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        name="confirm_password"
                        class="w-full px-4 py-2.5 rounded-lg border <?= isset($errors['confirm_password']) ? 'border-red-500' : 'border-gray-300' ?>"
                        placeholder="Ulangi password">

                    <?php if (isset($errors['confirm_password'])) : ?>
                        <p class="text-red-500 text-sm mt-1">
                            <?= $errors['confirm_password'] ?>
                        </p>
                    <?php endif; ?>
                </div>

                <button type="submit" name="register"
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition mt-2">
                    Daftar
                </button>
            </form>

            <hr class="border-gray-200 -mx-6 my-2">

            <div class="text-center text-sm text-gray-700 pt-1">
                Sudah punya akun? <a href="./login.php" class="text-blue-600 hover:underline">Login di sini</a>
            </div>
        </div>

    </div>
</body>

</html>