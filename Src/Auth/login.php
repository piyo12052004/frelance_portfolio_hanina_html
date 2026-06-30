<?php
require_once "../../Helper/function.php";

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: ../../index.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = login($_POST);
    $user = $result['user'] ?? null;

    if ($result['status']) {
        // Set notifikasi sukses masuk ke dashboard
        set_flash('success', 'Login Berhasil!', 'Selamat datang kembali, ' . $_SESSION['nama_lengkap']);
        header("Location: ../../index.php"); // Pastikan path redirect mengarah ke root index.php Anda
        exit;
    }
    $errors = $result['errors'] ?? [];
}
?>

<?php if (isset($user)) : ?>
    <!-- menyimpan data user ke localstorage -->
    <script>
        const user = <?= json_encode($user); ?>;
        localStorage.setItem("user", JSON.stringify(user));
    </script>
<?php endif; ?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gradient-to-tr from-indigo-600 via-purple-600 to-blue-500 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden">

        <div class="bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-600 pt-10 pb-8 text-center text-white px-6">
            <div class="flex justify-center mb-3">
                <div class="text-6xl opacity-90">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <h2 class="text-3xl font-semibold tracking-wide">Data Aset Barang</h2>
            <p class="text-blue-100 text-sm mt-1 opacity-80">Silakan login untuk melanjutkan</p>
        </div>

        <div class="p-8 space-y-6">
            <form method="POST" class="space-y-5">

                <div class="space-y-2">
                    <label class="flex items-center text-gray-600 text-sm font-medium gap-2">
                        <i class="fas fa-user text-xs"></i> Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        class="w-full px-4 py-3 rounded-lg border <?= isset($errors['username']) ? 'border-red-500' : 'border-cyan-100' ?> bg-cyan-50 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Masukkan username">

                    <?php if (isset($errors['username'])) : ?>
                        <p class="text-red-500 text-sm">
                            <?= $errors ? $errors['username'] : ''; ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="space-y-2" x-data="{ show: false }">

                    <label class="flex items-center text-gray-600 text-sm font-medium gap-2">
                        <i class="fas fa-lock text-xs"></i> Password
                    </label>

                    <div class="flex">

                        <input
                            :type="show ? 'text' : 'password'"
                            name="password"
                            class="w-full px-4 py-3 rounded-l-lg border <?= isset($errors['password']) ? 'border-red-500' : 'border-cyan-100' ?> bg-cyan-50 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="********">

                        <button
                            type="button"
                            @click="show = !show"
                            class="px-4 bg-cyan-50 border-y border-r border-cyan-100 rounded-r-lg text-gray-400 hover:text-gray-700">

                            <i
                                :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'">
                            </i>

                        </button>

                    </div>

                    <?php if (isset($errors['password'])) : ?>
                        <p class="text-red-500 text-sm">
                            <?= $errors['password']; ?>
                        </p>
                    <?php endif; ?>

                </div>

                <div class="flex items-center">
                    <!-- <input id="remember_me" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-400 border-gray-300 rounded cursor-pointer">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-600 cursor-pointer">
                        Ingat saya
                    </label> -->
                </div>

                <button type="submit" name="login"
                    class="w-full py-3 bg-sky-500 hover:bg-sky-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <hr class="border-gray-200 my-4">

            <div class="text-center text-sm">
                <a href="./registrasi.php" class="text-sky-500 hover:underline">
                    Belum punya akun? <span class="font-bold">Registrasi</span>
                </a>
            </div>
        </div>

    </div>
    <?php if (isset($_SESSION['flash'])) : ?>
        <script>
            Swal.fire({
                icon: "<?= $_SESSION['flash']['type']; ?>",
                title: "<?= $_SESSION['flash']['title']; ?>",
                text: "<?= $_SESSION['flash']['message']; ?>",
                timer: 3000,
                showConfirmButton: true
            });
        </script>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
</body>

</html>