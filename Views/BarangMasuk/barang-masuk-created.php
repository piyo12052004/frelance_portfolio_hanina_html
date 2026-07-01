<?php
session_start();

$title = "Tambah Barang Masuk";
$activeMenu = "barangMasuk";

require_once __DIR__ . '/../../Controllers/BarangMasukController.php';

$controller = new BarangMasukController();

$barang = $controller->getBarang();
$supplier = $controller->getSupplier();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->create($_POST);

    if ($result['status']) {
        $_SESSION['success'] = $result['message'];
        header("Location: barang-masuk.php");
        exit;
    } else {
        $_SESSION['error'] = $result['message'];
    }
}

require_once __DIR__ . '/../Layout/header.php';
?>

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold mb-6">Tambah Barang Masuk</h2>

    <!-- ALERT -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">

        <!-- BARANG -->
        <div>
            <label class="block text-sm font-medium mb-1">Barang</label>
            <select name="barang_id" class="w-full border px-3 py-2 rounded" required>
                <option value="">-- Pilih Barang --</option>
                <?php foreach ($barang as $b): ?>
                    <option value="<?= $b['id'] ?>">
                        <?= $b['nama_barang'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- SUPPLIER -->
        <div>
            <label class="block text-sm font-medium mb-1">Supplier</label>
            <select name="supplier_id" class="w-full border px-3 py-2 rounded">
                <option value="">-- Pilih Supplier --</option>
                <?php foreach ($supplier as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= $s['nama_supplier'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- TANGGAL -->
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal</label>
            <input type="date"
                   name="tanggal"
                   class="w-full border px-3 py-2 rounded"
                   required>
        </div>

        <!-- JUMLAH -->
        <div>
            <label class="block text-sm font-medium mb-1">Jumlah</label>
            <input type="number"
                   name="jumlah"
                   min="1"
                   class="w-full border px-3 py-2 rounded"
                   required>
        </div>

        <!-- HARGA -->
        <div>
            <label class="block text-sm font-medium mb-1">Harga</label>
            <input type="number"
                   name="harga"
                   min="0"
                   class="w-full border px-3 py-2 rounded">
        </div>

        <!-- KETERANGAN -->
        <div>
            <label class="block text-sm font-medium mb-1">Keterangan</label>
            <textarea name="keterangan"
                      rows="3"
                      class="w-full border px-3 py-2 rounded"
                      placeholder="Opsional"></textarea>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-end gap-2 pt-4 border-t">

            <a href="barang-masuk.php"
               class="px-4 py-2 border rounded hover:bg-gray-100">
                Kembali
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Simpan
            </button>

        </div>

    </form>

</div>

<?php require_once __DIR__ . '/../Layout/footer.php'; ?>