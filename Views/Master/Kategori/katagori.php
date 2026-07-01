<?php
session_start();

$title = "Update Kategori";
$activeMenu = "kategori";

require_once __DIR__ . '/../../../Controllers/KategoriController.php';
$kategori = new KategoriController();
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 5;
$search = $_GET['search'] ?? '';

$result = $kategori->getData($page, $limit, $search);
$kategoriList = $result['data'];

require_once __DIR__ . '/../../Layout/header.php';
?>
<div class="bg-white rounded-xl shadow-md">
    <?php if (isset($_SESSION['success'])) : ?>

        <div class="mb-5 rounded-lg bg-green-100 border border-green-300 p-4 text-green-700">

            <?= $_SESSION['success']; ?>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>


    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Data Kategori
            </h1>

            <p class="text-sm text-gray-500">
                Kelola seluruh kategori aset barang.
            </p>
        </div>

        <a
            href="katagori-created.php"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">

            + Tambah Kategori

        </a>

    </div>

    <!-- Search -->
    <div class="p-6">

        <form method="GET">

            <div class="relative max-w-sm">

                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Cari kategori..."
                    class="w-full rounded-lg border border-gray-300 pl-10 pr-4 py-2">

                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z" />

                </svg>

            </div>

        </form>

    </div>

    <!-- Table -->
    <div class="overflow-x-auto">

        <table class="min-w-full">

            <thead class="bg-gray-100">

                <tr>

                    <th class="px-6 py-3 text-left">No</th>

                    <th class="px-6 py-3 text-left">
                        Nama Kategori
                    </th>

                    <th class="px-6 py-3 text-left">
                        Created At
                    </th>

                    <th class="px-6 py-3 text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y">

                <?php if (!empty($kategoriList)) : ?>
                    <?php $no = 1; ?>

                    <?php foreach ($kategoriList as $row) : ?>
                        <tr>

                            <td class="px-6 py-4">
                                <?= $no++ ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= htmlspecialchars($row['nama_kategori']) ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= date('d M Y', strtotime($row['created_at'])) ?>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">

                                    <a href="katagori-update.php?id=<?= $row['id'] ?>"
                                        class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">
                                        Edit
                                    </a>

                                    <a href="katagori-delete.php?id=<?= $row['id'] ?>"
                                        onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">

                                        Hapus

                                    </a>

                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>

                <?php else : ?>

                    <tr>
                        <td colspan="4" class="text-center py-12 text-gray-400">
                            Belum ada data kategori.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>

    </div>

    <!-- Pagination -->

    <div class="flex justify-between items-center p-6 border-t">

        <span class="text-sm text-gray-500">
            Total <?= $result['total'] ?> Data
        </span>

        <div class="flex gap-2">

            <?php for ($i = 1; $i <= $result['total_page']; $i++) : ?>

                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                    class="px-4 py-2 border rounded-lg
    <?= $i == $result['page'] ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' ?>">

                    <?= $i ?>

                </a>

            <?php endfor; ?>

        </div>

    </div>

</div>

<?php
require_once __DIR__ . '/../../Layout/footer.php';
?>