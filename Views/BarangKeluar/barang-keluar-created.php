<?php
session_start();

$title = "Tambah Barang Keluar";
$activeMenu = "barangKeluar";

require_once __DIR__ . '/../../Controllers/BarangKeluarController.php';

$controller = new BarangKeluarController();

// ambil dropdown data
$barangList = $controller->getBarang();

// nomor transaksi otomatis
$nomor = $controller->getNomorTransaksi();

// proses submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $result = $controller->create($_POST);

    if ($result['status']) {
        $_SESSION['success'] = $result['message'];
        header("Location: barang-keluar.php");
        exit;
    } else {
        $_SESSION['error'] = $result['message'];
    }
}

require_once __DIR__ . '/../Layout/header.php';
?>

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold mb-6">Tambah Barang Keluar</h2>

    <!-- ALERT -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">

        <!-- NOMOR TRANSAKSI -->
        <div>
            <label class="block text-sm font-medium mb-1">Nomor Transaksi</label>
            <input type="text"
                   name="nomor_transaksi"
                   value="<?= $nomor ?>"
                   readonly
                   class="w-full border px-3 py-2 rounded bg-gray-100">
        </div>

        <!-- BARANG -->
        <div>
            <label class="block text-sm font-medium mb-1">Barang</label>
            <select name="barang_id" class="w-full border px-3 py-2 rounded" required>
                <option value="">-- Pilih Barang --</option>
                <?php foreach ($barangList as $b): ?>
                    <option value="<?= $b['id'] ?>">
                        <?= $b['nama_barang'] ?> (<?= $b['stok'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- TANGGAL -->
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal</label>
            <input type="date" name="tanggal" class="w-full border px-3 py-2 rounded" required>
        </div>

        <!-- JUMLAH -->
        <div>
            <label class="block text-sm font-medium mb-1">Jumlah</label>
            <input type="number" name="jumlah" min="1" class="w-full border px-3 py-2 rounded" required>
        </div>

        <!-- TUJUAN -->
        <div>
            <label class="block text-sm font-medium mb-1">Tujuan</label>
            <input type="text" name="tujuan" class="w-full border px-3 py-2 rounded" required>
        </div>

        <!-- KETERANGAN -->
        <div>
            <label class="block text-sm font-medium mb-1">Keterangan</label>
            <textarea name="keterangan" class="w-full border px-3 py-2 rounded"></textarea>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-end gap-2 pt-4 border-t">
            <a href="barang-keluar.php" class="px-4 py-2 border rounded hover:bg-gray-100">
                Kembali
            </a>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Simpan
            </button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../Layout/footer.php'; ?>