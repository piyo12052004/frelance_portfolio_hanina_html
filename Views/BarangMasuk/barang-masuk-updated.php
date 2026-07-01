<?php
session_start();

$title = "Update Barang Masuk";
$activeMenu = "barangMasuk";

require_once __DIR__ . '/../../Controllers/BarangMasukController.php';

$controller = new BarangMasukController();

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID tidak valid";
    header("Location: barang-masuk.php");
    exit;
}

$data = $controller->edit($id);

$barang = $controller->getBarang();
$supplier = $controller->getSupplier();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $result = $controller->update($_POST);

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

    <h2 class="text-xl font-bold mb-6">Update Barang Masuk</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">

        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <!-- BARANG -->
        <div>
            <label class="block text-sm font-medium mb-1">Barang</label>
            <select name="barang_id" class="w-full border px-3 py-2 rounded">
                <?php foreach ($barang as $b): ?>
                    <option value="<?= $b['id'] ?>"
                        <?= $b['id'] == $data['barang_id'] ? 'selected' : '' ?>>
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
                    <option value="<?= $s['id'] ?>"
                        <?= $s['id'] == $data['supplier_id'] ? 'selected' : '' ?>>
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
                   value="<?= $data['tanggal'] ?>"
                   class="w-full border px-3 py-2 rounded">
        </div>

        <!-- JUMLAH -->
        <div>
            <label class="block text-sm font-medium mb-1">Jumlah</label>
            <input type="number"
                   name="jumlah"
                   value="<?= $data['jumlah'] ?>"
                   class="w-full border px-3 py-2 rounded">
        </div>

        <!-- HARGA -->
        <div>
            <label class="block text-sm font-medium mb-1">Harga</label>
            <input type="number"
                   name="harga"
                   value="<?= $data['harga'] ?>"
                   class="w-full border px-3 py-2 rounded">
        </div>

        <!-- KETERANGAN -->
        <div>
            <label class="block text-sm font-medium mb-1">Keterangan</label>
            <textarea name="keterangan"
                      class="w-full border px-3 py-2 rounded"><?= $data['keterangan'] ?></textarea>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-end gap-2 pt-4 border-t">

            <a href="barang-masuk.php"
               class="px-4 py-2 border rounded hover:bg-gray-100">
                Kembali
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Update
            </button>

        </div>

    </form>

</div>

<?php require_once __DIR__ . '/../Layout/footer.php'; ?>