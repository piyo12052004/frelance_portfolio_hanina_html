<?php
session_start();

$title = "Supplier";
$activeMenu = "supplier";

require_once __DIR__ . '/../../../Controllers/SupplierController.php';

$supplier = new SupplierController();

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 5;
$search = $_GET['search'] ?? "";

$result = $supplier->getData($page, $limit, $search);
$supplierList = $result['data'];

require_once __DIR__ . '/../../Layout/header.php';
?>
<div class="bg-white rounded-xl shadow-md">

    <?php if (isset($_SESSION['success'])) : ?>

        <div class="mb-5 rounded-lg bg-green-100 border border-green-300 p-4 text-green-700">

            <?= $_SESSION['success']; ?>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>
    <div class="flex items-center justify-between p-6 border-b">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Data Supplier
            </h1>

            <p class="text-sm text-gray-500">
                Kelola seluruh data supplier.
            </p>
        </div>

        <a href="supplier-created.php"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">

            + Tambah Supplier

        </a>

    </div>

    <!-- Search -->
    <div class="p-6">
        <form method="GET">
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="Cari supplier..."
                class="w-full max-w-sm border rounded-lg px-4 py-2">
        </form>
    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto">
        <table class="min-w-full">

            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left">No</th>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Alamat</th>
                    <th class="px-6 py-3 text-left">Telepon</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Created</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                <?php if (!empty($supplierList)) : ?>
                    <?php $no = 1; ?>

                    <?php foreach ($supplierList as $row) : ?>
                        <tr>

                            <td class="px-6 py-4"><?= $no++ ?></td>

                            <td class="px-6 py-4">
                                <?= htmlspecialchars($row['nama_supplier']) ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= htmlspecialchars($row['alamat'] ?? '-') ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= htmlspecialchars($row['telepon'] ?? '-') ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= htmlspecialchars($row['email'] ?? '-') ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= date('d M Y', strtotime($row['created_at'])) ?>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex gap-2 justify-center">

                                    <a href="supplier-update.php?id=<?= $row['id'] ?>"
                                        class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">
                                        Edit
                                    </a>

                                    <a href="supplier-delete.php?id=<?= $row['id'] ?>"
                                        onclick="return confirm('Yakin hapus supplier?')"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        Hapus
                                    </a>

                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>

                <?php else : ?>
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            Belum ada data supplier.
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
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