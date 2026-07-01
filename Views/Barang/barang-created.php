<?php
session_start();

$title = "Tambah Barang";
$activeMenu = "barang";

require_once __DIR__ . '/../../Controllers/BarangController.php';

$controller = new BarangController();
$kodeBarang = $controller->getKodeBarang();

$getDataKategoriDanLokasi = $controller->getDataKategoriDanLokasi();

$kategori = $getDataKategoriDanLokasi['kategori'];
$lokasi   = $getDataKategoriDanLokasi['lokasi'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $result = $controller->create($_POST, $_FILES);
}

require_once __DIR__ . '/../Layout/header.php';
?>

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold mb-6">Tambah Data Barang</h2>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">

        <!-- KODE -->
        <div>
            <label class="text-sm font-medium">Kode Barang</label>
            <input type="text"
                   name="kode_barang"
                   value="<?= $kodeBarang ?>"
                   readonly
                   class="w-full border px-3 py-2 rounded bg-gray-100">
        </div>

        <!-- NAMA -->
        <div>
            <label class="text-sm font-medium">Nama Barang</label>
            <input type="text"
                   name="nama_barang"
                   required
                   class="w-full border px-3 py-2 rounded"
                   placeholder="Nama barang">
        </div>

        <!-- KATEGORI -->
        <div>
            <label class="text-sm font-medium">Kategori</label>
            <select name="kategori_id" class="w-full border px-3 py-2 rounded" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= $k['id'] ?>">
                        <?= $k['nama_kategori'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- LOKASI -->
        <div>
            <label class="text-sm font-medium">Lokasi</label>
            <select name="lokasi_id" class="w-full border px-3 py-2 rounded" required>
                <option value="">-- Pilih Lokasi --</option>
                <?php foreach ($lokasi as $l): ?>
                    <option value="<?= $l['id'] ?>">
                        <?= $l['nama_lokasi'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- MERK -->
        <div>
            <label class="text-sm font-medium">Merk</label>
            <input type="text" name="merk" class="w-full border px-3 py-2 rounded">
        </div>

        <!-- TIPE -->
        <div>
            <label class="text-sm font-medium">Tipe</label>
            <input type="text" name="tipe" class="w-full border px-3 py-2 rounded">
        </div>

        <!-- TAHUN -->
        <div>
            <label class="text-sm font-medium">Tahun</label>
            <input type="number"
                   name="tahun"
                   class="w-full border px-3 py-2 rounded"
                   min="2000"
                   max="<?= date('Y') ?>">
        </div>

        <!-- KONDISI -->
        <div>
            <label class="text-sm font-medium">Kondisi</label>
            <select name="kondisi" class="w-full border px-3 py-2 rounded">
                <option value="Baik">Baik</option>
                <option value="Rusak Ringan">Rusak Ringan</option>
                <option value="Rusak Berat">Rusak Berat</option>
            </select>
        </div>

        <!-- STOK -->
        <div>
            <label class="text-sm font-medium">Stok</label>
            <input type="number"
                   name="stok"
                   value="0"
                   min="0"
                   class="w-full border px-3 py-2 rounded">
        </div>

        <!-- FOTO -->
        <div>
            <label class="text-sm font-medium">Foto Barang</label>
            <input type="file"
                   name="foto"
                   accept="image/*"
                   class="w-full border px-3 py-2 rounded">
            <p class="text-xs text-gray-500 mt-1">
                Format: JPG, PNG. Maks 2MB
            </p>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-end gap-3 pt-4 border-t">

            <a href="barang.php"
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