<?php

$title = "Dashboard";
$activeMenu = "dashboard";

require_once __DIR__ . '/Controllers/DashboardController.php';

$controller = new DashboardController();

$data = $controller->index();

$totalBarang       = $data['totalBarang'];
$totalKategori     = $data['totalKategori'];
$totalBarangMasuk  = $data['totalBarangMasuk'];
$totalBarangKeluar = $data['totalBarangKeluar'];

require_once __DIR__ . "/Views/Layout/header.php";

?>

<div class="p-6 bg-gray-100 min-h-screen">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            Hello, <?= $_SESSION['nama_lengkap'] ?? 'Administrator'; ?> 👋
        </h1>
        <p class="text-gray-500 mt-1">
            Selamat datang di Sistem Inventaris Aset Barang.
        </p>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        <!-- Barang -->
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm">Total Barang</p>

            <h2 class="text-3xl font-bold mt-2">
                <?= $totalBarang ?? 0 ?>
            </h2>

            <p class="text-xs text-gray-400 mt-2">
                Data seluruh barang inventaris
            </p>
        </div>

        <!-- Kategori -->
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Kategori</p>

            <h2 class="text-3xl font-bold mt-2">
                <?= $totalKategori ?? 0 ?>
            </h2>

            <p class="text-xs text-gray-400 mt-2">
                Total kategori barang
            </p>
        </div>

        <!-- Barang Masuk -->
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500">
            <p class="text-gray-500 text-sm">Barang Masuk</p>

            <h2 class="text-3xl font-bold mt-2">
                <?= $totalBarangMasuk ?? 0 ?>
            </h2>

            <p class="text-xs text-gray-400 mt-2">
                Total transaksi barang masuk
            </p>
        </div>

        <!-- Barang Keluar -->
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-red-500">
            <p class="text-gray-500 text-sm">Barang Keluar</p>

            <h2 class="text-3xl font-bold mt-2">
                <?= $totalBarangKeluar ?? 0 ?>
            </h2>

            <p class="text-xs text-gray-400 mt-2">
                Total transaksi barang keluar
            </p>
        </div>

    </div>

    <!-- Content -->
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">


        <!-- Informasi -->
        <div class="bg-white rounded-xl shadow">

            <div class="border-b px-6 py-4">
                <h2 class="font-semibold text-lg">
                    Informasi Sistem
                </h2>
            </div>

            <div class="p-6 space-y-4">

                <div class="flex justify-between">
                    <span class="text-gray-500">
                        PHP
                    </span>

                    <span class="font-semibold">
                        <?= phpversion(); ?>
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">
                        Tanggal
                    </span>

                    <span class="font-semibold">
                        <?= date('d M Y'); ?>
                    </span>
                </div>


                <div class="flex justify-between">
                    <span class="text-gray-500">
                        Login Sebagai
                    </span>

                    <span class="font-semibold">
                        <?= $_SESSION['role'] ?? '-'; ?>
                    </span>
                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once __DIR__ . "/Views/Layout/footer.php";

?>