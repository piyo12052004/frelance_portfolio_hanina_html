<?php
session_start();

$title = "Update Barang";
$activeMenu = "barang";

require_once __DIR__ . '/../../Controllers/BarangController.php';

$controller = new BarangController();

// ambil id dari URL
$id = $_GET['id'] ?? 0;

// ambil data detail barang
$barang = $controller->getById($id);

// data dropdown
$getDataKategoriDanLokasi = $controller->getDataKategoriDanLokasi();
$kategori = $getDataKategoriDanLokasi['kategori'];
$lokasi   = $getDataKategoriDanLokasi['lokasi'];

// proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->update($_POST, $_FILES, $id);
}

require_once __DIR__ . '/../Layout/header.php';
?>

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold mb-6">Update Data Barang</h2>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">

        <!-- KODE -->
        <div>
            <label class="text-sm font-medium">Kode Barang</label>
            <input type="text"
                   name="kode_barang"
                   value="<?= $barang['kode_barang'] ?>"
                   readonly
                   class="w-full border px-3 py-2 rounded bg-gray-100">
        </div>

        <!-- NAMA -->
        <div>
            <label class="text-sm font-medium">Nama Barang</label>
            <input type="text"
                   name="nama_barang"
                   value="<?= $barang['nama_barang'] ?>"
                   required
                   class="w-full border px-3 py-2 rounded">
        </div>

        <!-- KATEGORI -->
        <div>
            <label class="text-sm font-medium">Kategori</label>
            <select name="kategori_id" class="w-full border px-3 py-2 rounded" required>

                <?php foreach ($kategori as $k): ?>
                    <option value="<?= $k['id'] ?>"
                        <?= $barang['kategori_id'] == $k['id'] ? 'selected' : '' ?>>
                        <?= $k['nama_kategori'] ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <!-- LOKASI -->
        <div>
            <label class="text-sm font-medium">Lokasi</label>
            <select name="lokasi_id" class="w-full border px-3 py-2 rounded" required>

                <?php foreach ($lokasi as $l): ?>
                    <option value="<?= $l['id'] ?>"
                        <?= $barang['lokasi_id'] == $l['id'] ? 'selected' : '' ?>>
                        <?= $l['nama_lokasi'] ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <!-- MERK -->
        <div>
            <label class="text-sm font-medium">Merk</label>
            <input type="text"
                   name="merk"
                   value="<?= $barang['merk'] ?>"
                   class="w-full border px-3 py-2 rounded">
        </div>

        <!-- TIPE -->
        <div>
            <label class="text-sm font-medium">Tipe</label>
            <input type="text"
                   name="tipe"
                   value="<?= $barang['tipe'] ?>"
                   class="w-full border px-3 py-2 rounded">
        </div>

        <!-- TAHUN -->
        <div>
            <label class="text-sm font-medium">Tahun</label>
            <input type="number"
                   name="tahun"
                   value="<?= $barang['tahun'] ?>"
                   class="w-full border px-3 py-2 rounded">
        </div>

        <!-- KONDISI -->
        <div>
            <label class="text-sm font-medium">Kondisi</label>
            <select name="kondisi" class="w-full border px-3 py-2 rounded">

                <option value="Baik" <?= $barang['kondisi'] == 'Baik' ? 'selected' : '' ?>>Baik</option>
                <option value="Rusak Ringan" <?= $barang['kondisi'] == 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                <option value="Rusak Berat" <?= $barang['kondisi'] == 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>

            </select>
        </div>

        <!-- STOK -->
        <div>
            <label class="text-sm font-medium">Stok</label>
            <input type="number"
                   name="stok"
                   value="<?= $barang['stok'] ?>"
                   min="0"
                   class="w-full border px-3 py-2 rounded">
        </div>

        <!-- FOTO LAMA -->
        <?php if (!empty($barang['foto'])): ?>
            <div>
                <label class="text-sm font-medium">Foto Saat Ini</label>
                <img src="../../Uploads/barang/<?= $barang['foto'] ?>"
                     class="w-32 h-32 object-cover rounded border mt-2">
            </div>
        <?php endif; ?>

        <!-- FOTO BARU -->
        <div>
            <label class="text-sm font-medium">Ganti Foto (opsional)</label>
            <input type="file"
                   name="foto"
                   accept="image/*"
                   class="w-full border px-3 py-2 rounded">

            <p class="text-xs text-gray-500 mt-1">
                Kosongkan jika tidak ingin mengganti foto
            </p>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-end gap-3 pt-4 border-t">

            <a href="barang.php"
               class="px-4 py-2 border rounded hover:bg-gray-100">
                Kembali
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                Update
            </button>

        </div>

    </form>

</div>

<?php require_once __DIR__ . '/../Layout/footer.php'; ?>