<?php

$title = "Barang";
$activeMenu = "barang";

require_once "../../Helper/function_barang.php";

// get data
$result = getDataBarang($_GET);
$barang = $result['data'];
$pagination = $result['pagination'];
$no = $pagination['offset'] ?? 0;
$no = $no + 1;

// get kode barang
$kodeBarang = getKodeBarang();
$getDataLokasiDanKategori = getDataLokasiDanKategori();
$kategori = $getDataLokasiDanKategori['kategori'];
$lokasi   = $getDataLokasiDanKategori['lokasi'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    switch ($_POST['action'] ?? '') {

        case 'created-data-barang':
            $result = createdDataBarang($_POST);
            break;

        case 'update-data-barang':
            $result = updateDataBarang($_POST);
            break;
        case 'delete-data-barang-by-id':
            $result = deleteDataBarang($_POST);
            break;

        default:
            $result = [
                'status' => false,
                'message' => 'Action tidak dikenali.'
            ];
            break;
    }

    if ($result !== null) {

        if ($result['status']) {
            set_flash('success', 'Berhasil', $result['message']);
        } else {
            set_flash('error', 'Gagal', $result['message']);
        }

        header("Location: barang.php");
        exit;
    }
}

require_once __DIR__ . '/../Layout/header.php';
?>

<div>
    <div class="p-6 bg-gray-50 min-h-screen">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Data Barang</h2>
                <p class="text-sm text-gray-500">Kelola data barang inventaris</p>
            </div>

            <a @click="$store.modal.show({
        title: 'Tambah Data',
        template: 'modal-barang-created-barang',
        size: 'max-w-2xl'
    })"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                + Tambah Data
            </a>
        </div>

        <!-- SEARCH & FILTER -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">

            <!-- SEARCH -->
            <form method="GET" class="relative">
                <input type="text"
                    name="search"
                    value="<?= $_GET['search'] ?? '' ?>"
                    placeholder="Cari nama barang / kode..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none bg-white shadow-sm">

                <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
            </form>
        </div>

        <!-- TABLE CARD -->
        <div class="bg-white rounded-xl shadow overflow-hidden">

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">

                    <!-- HEAD -->
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Kode</th>
                            <th class="px-4 py-3 text-left">Nama Barang</th>
                            <th class="px-4 py-3 text-left">Kategori</th>
                            <th class="px-4 py-3 text-left">Lokasi</th>
                            <th class="px-4 py-3 text-left">Kondisi</th>
                            <th class="px-4 py-3 text-left">Stok</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <!-- BODY -->
                    <tbody class="divide-y">

                        <?php foreach ($barang as $row) : ?>

                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-4 py-3 text-gray-600"><?= $no++ ?></td>

                                <td class="px-4 py-3 font-semibold text-gray-800">
                                    <?= $row['kode_barang'] ?>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">
                                        <?= $row['nama_barang'] ?>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        <?= $row['merk'] ?? '-' ?> • <?= $row['tahun'] ?? '-' ?>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                        <?= $row['nama_kategori'] ?? '-' ?>
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-gray-600">
                                    <?= $row['nama_lokasi'] ?? '-' ?>
                                </td>

                                <!-- KONDISI -->
                                <td class="px-4 py-3">
                                    <?php
                                    $kondisi = $row['kondisi'];

                                    if ($kondisi == 'Baik') {
                                        $cls = "bg-green-100 text-green-700";
                                    } elseif ($kondisi == 'Rusak Ringan') {
                                        $cls = "bg-yellow-100 text-yellow-700";
                                    } else {
                                        $cls = "bg-red-100 text-red-700";
                                    }
                                    ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= $cls ?>">
                                        <?= $kondisi ?>
                                    </span>
                                </td>

                                <td class="px-4 py-3 font-bold text-gray-800">
                                    <?= $row['stok'] ?>
                                </td>

                                <!-- ACTION -->
                                <td>

                                    <div class="flex items-center justify-center gap-2">

                                        <!-- Edit -->
                                        <button
                                            type="button"
                                            @click="$store.modal.show({
            title: 'Edit Data',
            template: 'modal-barang-edit-data-barang',
            size: 'max-w-2xl',
            data: <?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>
        })"
                                            class="inline-flex items-center justify-center w-24 h-8 rounded-lg bg-amber-500 hover:bg-amber-600 text-sm font-medium text-white transition">

                                            Edit

                                        </button>

                                        <!-- Hapus -->
                                        <form method="POST">

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="delete-data-barang-by-id">

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= $row['id']; ?>">

                                            <button
                                                type="button"
                                                @click="$store.alert.confirmDelete($el.form)"
                                                class="inline-flex mt-4 items-center justify-center w-24 h-8 rounded-lg bg-red-600 hover:bg-red-700 text-sm font-medium text-white transition">

                                                Hapus

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-3 px-4 py-3 border-t">

                <div class="text-sm text-gray-500">
                    Page <span class="font-semibold"><?= $pagination['page'] ?></span>
                    dari <span class="font-semibold"><?= $pagination['total_page'] ?></span>
                </div>

                <div class="flex gap-2">

                    <!-- PREV -->
                    <?php if ($pagination['has_prev']) : ?>
                        <a href="?page=<?= $pagination['page'] - 1 ?>&search=<?= $_GET['search'] ?? '' ?>"
                            class="px-3 py-1 rounded-lg bg-gray-200 hover:bg-gray-300 text-sm">
                            Prev
                        </a>
                    <?php endif; ?>

                    <!-- NEXT -->
                    <?php if ($pagination['has_next']) : ?>
                        <a href="?page=<?= $pagination['page'] + 1 ?>&search=<?= $_GET['search'] ?? '' ?>"
                            class="px-3 py-1 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                            Next
                        </a>
                    <?php endif; ?>

                </div>

            </div>

        </div>
    </div>

</div>
<!-- update barang -->
<template id="modal-barang-edit-data-barang">

    <form method="POST"
        x-data="{ form: {...$store.modal.data} }"
        class="space-y-4">

        <input type="hidden" name="action" value="update-data-barang">
        <input type="hidden" name="id" x-model="form.id">

        <!-- KODE BARANG -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Kode Barang
            </label>

            <input
                type="text"
                name="kode_barang"
                x-model="form.kode_barang"
                readonly
                class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-gray-100">
        </div>

        <!-- NAMA BARANG -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Nama Barang
            </label>

            <input
                type="text"
                name="nama_barang"
                x-model="form.nama_barang"
                placeholder="Masukkan nama barang"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required>
        </div>

        <!-- KATEGORI -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Kategori
            </label>

            <select
                name="kategori_id"
                x-model="form.kategori_id"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required>

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
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Lokasi
            </label>

            <select
                name="lokasi_id"
                x-model="form.lokasi_id"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required>

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
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Merk
            </label>

            <input
                type="text"
                name="merk"
                x-model="form.merk"
                placeholder="Contoh: Samsung / Asus / dll"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- TIPE -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Tipe
            </label>

            <input
                type="text"
                name="tipe"
                x-model="form.tipe"
                placeholder="Tipe Barang"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- TAHUN -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Tahun
            </label>

            <input
                type="number"
                name="tahun"
                x-model="form.tahun"
                placeholder="Contoh: 2024"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- KONDISI -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Kondisi
            </label>

            <select
                name="kondisi"
                x-model="form.kondisi"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required>

                <option value="">-- Pilih Kondisi --</option>
                <option value="Baik">Baik</option>
                <option value="Rusak Ringan">Rusak Ringan</option>
                <option value="Rusak Berat">Rusak Berat</option>

            </select>
        </div>

        <!-- STOK -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Stok
            </label>

            <input
                type="number"
                name="stok"
                x-model="form.stok"
                placeholder="Jumlah stok"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-end gap-3 pt-3">

            <button
                type="button"
                @click="$store.modal.close()"
                class="px-4 py-2 rounded-lg border hover:bg-gray-100">
                Batal
            </button>

            <button
                type="submit"
                class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">
                Update
            </button>

        </div>

    </form>

</template>

<!-- created barang -->
<template id="modal-barang-created-barang">

    <form method="POST" class="space-y-4">

        <input type="hidden" name="action" value="created-data-barang">

        <!-- KODE BARANG -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Kode Barang
            </label>

            <input type="text"
                name="kode_barang"
                value="<?= $kodeBarang ?>"
                readonly
                class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-gray-100">
        </div>

        <!-- NAMA BARANG -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Nama Barang
            </label>

            <input type="text"
                name="nama_barang"
                placeholder="Masukkan nama barang"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required>
        </div>

        <!-- KATEGORI -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Kategori
            </label>

            <select name="kategori_id" class="w-full border rounded-lg px-4 py-2">
                <option value="">-- Pilih Kategori --</option>

                <?php foreach ($kategori as $k) : ?>
                    <option value="<?= $k['id'] ?>">
                        <?= $k['nama_kategori'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- LOKASI -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Lokasi
            </label>

            <select name="lokasi_id" class="w-full border rounded-lg px-4 py-2">
                <option value="">-- Pilih Lokasi --</option>

                <?php foreach ($lokasi as $l) : ?>
                    <option value="<?= $l['id'] ?>">
                        <?= $l['nama_lokasi'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- MERK -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Merk
            </label>

            <input type="text"
                name="merk"
                placeholder="Contoh: Samsung / Asus / dll"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Tipe
            </label>

            <input type="text"
                name="tipe"
                placeholder="Tipe Barang"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- TAHUN -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Tahun
            </label>

            <input type="number"
                name="tahun"
                placeholder="Contoh: 2024"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- KONDISI -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Kondisi
            </label>

            <select name="kondisi"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required>
                <option value="">-- Pilih Kondisi --</option>
                <option value="Baik">Baik</option>
                <option value="Rusak Ringan">Rusak Ringan</option>
                <option value="Rusak Berat">Rusak Berat</option>
            </select>
        </div>

        <!-- STOK -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Stok
            </label>

            <input type="number"
                name="stok"
                placeholder="Jumlah stok"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-end gap-3 pt-3">

            <button type="button"
                @click="$store.modal.close()"
                class="px-4 py-2 rounded-lg border hover:bg-gray-100">
                Batal
            </button>

            <button type="submit"
                class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">
                Simpan
            </button>

        </div>

    </form>

</template>


<?php

require_once __DIR__ . '/../Layout/footer.php';
?>