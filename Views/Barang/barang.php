<?php

$title = "Barang";
$activeMenu = "barang";

require_once __DIR__ . '/../../Controllers/BarangController.php';

$controller = new BarangController();

$page = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? '';

$result = $controller->getData(10, $page, $search);

$barang = $result['data'];
$pagination = $result['pagination'];

$no = $pagination['offset'] + 1;

require_once __DIR__ . '/../Layout/header.php';
?>

<div class="p-6 bg-gray-50 min-h-screen">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">

        <div>
            <h2 class="text-2xl font-bold">Data Barang</h2>
            <p class="text-sm text-gray-500">Kelola data barang inventaris</p>
        </div>

        <div class="flex items-center gap-3">

            <a href="barang-export-pdf.php" target="_blank"
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

            <a href="barang-export-excel.php" target="_blank"
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

            <a href="barang-created.php" 
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2">

                <!-- Icon Plus -->
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4v16m8-8H4" />

                </svg>

                Tambah Barang
            </a>

        </div>

    </div>

    <!-- SEARCH -->
    <form method="GET" class="mb-4">
        <input type="text"
            name="search"
            value="<?= $_GET['search'] ?? '' ?>"
            placeholder="Cari barang..."
            class="w-full md:w-1/3 border px-3 py-2 rounded-lg">
    </form>
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
    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">

            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">No</th>
                    <th class="p-3 text-left">Kode</th>
                    <th class="p-3 text-left">Nama</th>
                    <th class="p-3 text-left">Kategori</th>
                    <th class="p-3 text-left">Lokasi</th>
                    <th class="p-3 text-left">Stok</th>
                    <th class="p-3 text-left">Foto</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($barang as $row) : ?>
                    <tr class="border-t">

                        <td class="p-3"><?= $no++ ?></td>
                        <td class="p-3"><?= $row['kode_barang'] ?></td>

                        <td class="p-3">
                            <div class="font-medium"><?= $row['nama_barang'] ?></div>
                            <div class="text-xs text-gray-400">
                                <?= $row['merk'] ?? '-' ?> | <?= $row['tahun'] ?? '-' ?>
                            </div>
                        </td>

                        <td class="p-3"><?= $row['nama_kategori'] ?? '-' ?></td>
                        <td class="p-3"><?= $row['nama_lokasi'] ?? '-' ?></td>
                        <td class="p-3 font-bold"><?= $row['stok'] ?></td>
                        <td class="px-4 py-3 text-center">

                            <?php if (!empty($row['foto']) && file_exists("../../Uploads/barang/" . $row['foto'])) : ?>

                                <img
                                    src="../../Uploads/barang/<?= htmlspecialchars($row['foto']) ?>"
                                    alt="<?= htmlspecialchars($row['nama_barang']) ?>"
                                    class="w-16 h-16 object-cover rounded-lg border shadow">

                            <?php else : ?>

                                <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded-lg border text-xs text-gray-400">
                                    Tidak Ada
                                </div>

                            <?php endif; ?>

                        </td>

                        <td class="p-3 text-center flex justify-center gap-2">

                            <a href="barang-update.php?id=<?= $row['id'] ?>"
                                class="bg-yellow-500 text-white px-3 py-1 rounded">
                                Edit
                            </a>

                            <a href="barang-deleted.php?id=<?= $row['id'] ?>"
                                onclick="return confirm('Yakin hapus Barang?')"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Hapus
                            </a>

                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</div>

<?php require_once __DIR__ . '/../Layout/footer.php'; ?>