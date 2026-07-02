<?php

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_GET['menu'])) {

    if ($_GET['menu'] == 'master') {

        $_SESSION['masterOpen'] = !($_SESSION['masterOpen'] ?? false);
    }
}

$masterOpen = $_SESSION['masterOpen'] ?? false;

// Otomatis buka jika sedang berada di halaman master
if (in_array($activeMenu, ['kategori', 'lokasi', 'supplier'])) {
    $masterOpen = true;
}
?>
<aside
    x-data="{ masterOpen: <?= in_array($activeMenu, ['kategori', 'lokasi', 'supplier']) ? 'true' : 'false' ?> }"
    class="fixed top-0 left-0 w-64 h-screen bg-gradient-to-b from-blue-600 to-purple-600 text-white shadow-xl">

    <div class="p-8 text-center border-b border-white/20">
        <h2 class="text-xl font-bold">Data Aset Barang</h2>
        <p class="text-sm text-white/80">Hanina Nur Faizah</p>
    </div>

    <div class="mt-8 px-4 space-y-2">

        <!-- Dashboard -->
        <a href="<?= BASE_URL ?>/index.php"
            class="flex items-center gap-3 p-3 rounded-lg transition <?= $activeMenu === 'dashboard' ? 'bg-white/20' : 'hover:bg-white/20' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>

            Dashboard
        </a>

        <!-- Master -->
        <a
            href="?menu=master"
            class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-white/20 transition">

            <div class="flex items-center gap-3">

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                </svg>


                <span>Master</span>

            </div>

            <span>
                <?= $masterOpen ? '▲' : '▼'; ?>
            </span>

        </a>

        <!-- Dropdown -->
        <?php if ($masterOpen) : ?>

            <div class="ml-6 space-y-1">

                <a href="<?= BASE_URL ?>/Views/Master/Kategori/katagori.php"
                    class="block p-2 rounded-lg transition <?= $activeMenu === 'kategori' ? 'bg-white/20' : 'hover:bg-white/20' ?>">
                    Kategori
                </a>

                <a href="<?= BASE_URL ?>/Views/Master/Lokasi/lokasi.php"
                    class="block p-2 rounded-lg transition <?= $activeMenu === 'lokasi' ? 'bg-white/20' : 'hover:bg-white/20' ?>">
                    Lokasi
                </a>

                <a href="<?= BASE_URL ?>/Views/Master/Supplier/supplier.php"
                    class="block p-2 rounded-lg transition <?= $activeMenu === 'supplier' ? 'bg-white/20' : 'hover:bg-white/20' ?>">
                    Supplier
                </a>

            </div>

        <?php endif; ?>
        <!-- Barang -->
        <a href="<?= BASE_URL ?>/Views/Barang/barang.php"
            class="flex items-center gap-3 p-3 rounded-lg transition <?= $activeMenu === 'barang' ? 'bg-white/20' : 'hover:bg-white/20' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z" />
            </svg>
            Barang
        </a>
        <a href="<?= BASE_URL ?>/Views/BarangMasuk/barang-masuk.php"
            class="flex items-center gap-3 p-3 rounded-lg transition <?= $activeMenu === 'barangMasuk' ? 'bg-white/20' : 'hover:bg-white/20' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v6.75m0 0-3-3m3 3 3-3m-8.25 6a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
            </svg>


            Barang Masuk
        </a>
        <a href="<?= BASE_URL ?>/Views/BarangKeluar/barang-keluar.php"
            class="flex items-center gap-3 p-3 rounded-lg transition <?= $activeMenu === 'barangKeluar' ? 'bg-white/20' : 'hover:bg-white/20' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
            </svg>

            Barang Keluar
        </a>

    </div>

</aside>