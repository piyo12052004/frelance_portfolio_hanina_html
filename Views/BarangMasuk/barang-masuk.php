<?php

$title = "Barang Masuk";
$activeMenu = "barangMasuk";

require_once __DIR__ . '/../../Controllers/BarangMasukController.php';

$controller = new BarangMasukController();

$result = $controller->getData($_GET);
$data = $result['data'] ?? [];

require_once __DIR__ . '/../Layout/header.php';
?>

<div class="p-6 bg-gray-50 min-h-screen">
    <?php if (isset($_SESSION['success'])) : ?>

        <div class="mb-5 rounded-lg bg-green-100 border border-green-300 p-4 text-green-700">

            <?= $_SESSION['success']; ?>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>
    <?php if (isset($_SESSION['error'])) : ?>

        <div class="mb-5 rounded-lg bg-red-100 border border-red-300 p-4 text-red-700">

            <?= $_SESSION['error']; ?>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold">Barang Masuk</h2>
            <p class="text-sm text-gray-500">Kelola data barang masuk</p>
        </div>
        <div class="flex items-center gap-3">

            <a href="barang-masuk-pdf.php" target="_blank"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">

                <!-- Icon Export -->
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 16V4m0 12l-4-4m4 4l4-4M4 20h16" />

                </svg>

                Export PDF
            </a>

            <a href="barang-masuk-excel.php" target="_blank"
                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center gap-2">

                <!-- Icon Export -->
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 16V4m0 12l-4-4m4 4l4-4M4 20h16" />

                </svg>

                Export EXCEL
            </a>

            <a href="barang-masuk-created.php"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + Tambah
            </a>

        </div>
    </div>

    <!-- SEARCH -->
    <form method="GET" class="mb-4">
        <input type="text"
            name="search"
            value="<?= $_GET['search'] ?? '' ?>"
            placeholder="Cari barang / supplier..."
            class="w-full md:w-1/3 border px-3 py-2 rounded-lg">
    </form>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">

            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">No</th>
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">Barang</th>
                    <th class="p-3 text-left">Supplier</th>
                    <th class="p-3 text-left">Jumlah</th>
                    <th class="p-3 text-left">Harga</th>
                    <th class="p-3 text-left">Total</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php $no = 1;
                foreach ($data as $row): ?>
                    <tr class="border-t">

                        <td class="p-3"><?= $no++ ?></td>

                        <td class="p-3">
                            <?= date('d-m-Y', strtotime($row['tanggal'])) ?>
                        </td>

                        <td class="p-3"><?= $row['nama_barang'] ?></td>

                        <td class="p-3"><?= $row['nama_supplier'] ?? '-' ?></td>

                        <td class="p-3"><?= $row['jumlah'] ?></td>

                        <td class="p-3">
                            Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                        </td>

                        <td class="p-3 font-bold text-green-600">
                            Rp <?= number_format($row['jumlah'] * $row['harga'], 0, ',', '.') ?>
                        </td>

                        <td class="p-3 text-center">

                            <!-- EDIT (PAGE BIASA) -->
                            <a href="barang-masuk-updated.php?id=<?= $row['id'] ?>"
                                class="bg-yellow-500 text-white px-4 py-1 mr-2 rounded">
                                Edit
                            </a>

                            <!-- DELETE -->
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin'): ?>
                                <a href="barang-masuk-deleted.php?id=<?= $row['id'] ?>"
                                    onclick="return confirm('Yakin hapus Barang Masuk?')"
                                    class="px-4 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    Hapus
                                </a>
                            <?php endif; ?>


                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</div>

<?php require_once __DIR__ . '/../Layout/footer.php'; ?>