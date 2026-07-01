<?php
session_start();
$title = "Barang Keluar";
$activeMenu = "barangKeluar";

require_once __DIR__ . '/../../Controllers/BarangKeluarController.php';

$controller = new BarangKeluarController();

$result = $controller->getData($_GET);
$data = $result['data'];
$pagination = $result['pagination'];

$no = $pagination['offset'] + 1;

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
            <h2 class="text-2xl font-bold text-gray-800">Barang Keluar</h2>
            <p class="text-sm text-gray-500">Data distribusi barang keluar</p>
        </div>

        <a href="barang-keluar-created.php"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Tambah Barang Keluar
        </a>
    </div>

    <!-- SEARCH -->
    <form method="GET" class="mb-4">
        <input type="text"
            name="search"
            value="<?= $_GET['search'] ?? '' ?>"
            placeholder="Cari nomor transaksi / barang..."
            class="w-full md:w-1/3 border px-3 py-2 rounded-lg">
    </form>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">No Transaksi</th>
                        <th class="p-3 text-left">Barang</th>
                        <th class="p-3 text-left">Tanggal</th>
                        <th class="p-3 text-left">Jumlah</th>
                        <th class="p-3 text-left">Tujuan</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr class="border-t hover:bg-gray-50">

                            <td class="p-3"><?= $no++ ?></td>

                            <td class="p-3 font-semibold">
                                <?= $row['nomor_transaksi'] ?>
                            </td>

                            <td class="p-3">
                                <div class="font-medium">
                                    <?= $row['nama_barang'] ?>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <?= $row['kode_barang'] ?? '' ?>
                                </div>
                            </td>

                            <td class="p-3">
                                <?= $row['tanggal'] ?>
                            </td>

                            <td class="p-3 font-bold text-red-600">
                                -<?= $row['jumlah'] ?>
                            </td>

                            <td class="p-3">
                                <?= $row['tujuan'] ?>
                            </td>

                            <!-- ACTION -->
                            <td class="p-3 text-center">

                                <div class="flex justify-center gap-2">

                                    <a href="barang-keluar-updated.php?id=<?= $row['id'] ?>"
                                        class="px-3 py-1 bg-yellow-500 text-white rounded">
                                        Edit
                                    </a>

                                    <a href="barang-keluar-deleted.php?id=<?= $row['id'] ?>"
                                        onclick="return confirm('Hapus data?')"
                                        class="px-3 py-1 bg-red-600 text-white rounded">
                                        Hapus
                                    </a>

                                </div>

                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    </div>

    <!-- PAGINATION -->
    <div class="flex justify-between items-center mt-4 text-sm">

        <div>
            Page <?= $pagination['page'] ?> dari <?= $pagination['total_page'] ?>
        </div>

        <div class="flex gap-2">

            <?php if ($pagination['has_prev']): ?>
                <a href="?page=<?= $pagination['page'] - 1 ?>&search=<?= $_GET['search'] ?? '' ?>"
                    class="px-3 py-1 bg-gray-200 rounded">
                    Prev
                </a>
            <?php endif; ?>

            <?php if ($pagination['has_next']): ?>
                <a href="?page=<?= $pagination['page'] + 1 ?>&search=<?= $_GET['search'] ?? '' ?>"
                    class="px-3 py-1 bg-blue-600 text-white rounded">
                    Next
                </a>
            <?php endif; ?>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../Layout/footer.php'; ?>