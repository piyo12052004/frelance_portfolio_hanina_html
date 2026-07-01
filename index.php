<?php
$title = "Dashboard";
$activeMenu = "dashboard";
require_once "./Helper/function_dashboard.php";

$totalBarang        = $dashboard->getTotalBarang();
$totalBarangMasuk   = $dashboard->getTotalBarangMasuk();
$totalBarangKeluar  = $dashboard->getTotalBarangKeluar();
$totalStok          = $dashboard->getTotalStok();

require_once __DIR__ . '/Src/Layout/header.php';
?>

<!-- Card Statistik -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

    <div class="bg-blue-600 rounded-xl p-6 text-white shadow">
        <h2 class="text-4xl font-bold">
            <?= $totalBarang ?>
        </h2>
        <p class="mt-2 text-blue-100">
            Total Barang
        </p>
    </div>

    <div class="bg-green-600 rounded-xl p-6 text-white shadow">
        <h2 class="text-4xl font-bold">
            <?= $totalBarangMasuk ?>
        </h2>
        <p class="mt-2 text-green-100">
            Barang Masuk
        </p>
    </div>

    <div class="bg-red-600 rounded-xl p-6 text-white shadow">
        <h2 class="text-4xl font-bold">
            <?= $totalBarangKeluar ?>
        </h2>
        <p class="mt-2 text-red-100">
            Barang Keluar
        </p>
    </div>

    <div class="bg-yellow-500 rounded-xl p-6 text-white shadow">
        <h2 class="text-4xl font-bold">
            <?= $totalStok ?>
        </h2>
        <p class="mt-2 text-yellow-100">
            Stok Tersedia
        </p>
    </div>

</div>

<!-- Informasi Sistem -->
<div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mt-8">
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold mb-4">
            Ringkasan Hari Ini
        </h3>

        <table class="w-full text-sm">
            <tbody class="divide-y">
                <tr>
                    <td class="py-3">Barang Masuk</td>
                    <td class="text-right font-semibold text-green-600"> <?= $totalBarangMasuk ?></td>
                </tr>

                <tr>
                    <td class="py-3">Barang Keluar</td>
                    <td class="text-right font-semibold text-red-600"> <?= $totalBarangKeluar ?></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<?php
require_once __DIR__ . '/Src/Layout/footer.php';
?>