<?php

$title = "Barang Keluar";
$activeMenu = "barangKeluar";

require_once "../../Helper/function_barang_keluar.php";

// nanti
$result = getDataBarangKeluar($_GET);
$barangKeluar = $result['data'];
$pagination   = $result['pagination'];
$no = $pagination['offset'] + 1;

// get data transaksi
$getNomor = getNomorTransaksiBarangKeluar();
if ($getNomor['status']) {
    $nomorTransaksi = $getNomor['data'];
} else {
    die($getNomor['message']);
}

$getBarang = getDataBarangForBarangKeluar();

if ($getBarang['status']) {
    $barang = $getBarang['data'];
} else {
    $barang = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    switch ($_POST['action'] ?? '') {

        case 'created-data-barang-keluar':
            $result = createdDataBarangKeluar($_POST);
            break;
        case 'update-data-barang-keluar':
            $result = updateDataBarangKeluar($_POST);
            break;
        case 'delete-data-barang-keluar-by-id':
            $result = deleteDataBarangKeluar($_POST['id']);
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

        header("Location: barang-keluar.php");
        exit;
    }
}

require_once __DIR__ . '/../Layout/header.php';
?>

<div class="p-6 bg-gray-50 min-h-screen">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Barang Keluar
            </h1>

            <p class="text-sm text-gray-500">
                Kelola transaksi barang keluar
            </p>
        </div>

        <button
            type="button"
            @click="$store.modal.show({
                title:'Tambah Barang Keluar',
                template:'modal-barang-keluar-created',
                size:'max-w-3xl'
            })"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow">

            + Tambah Data

        </button>

    </div>

    <!-- SEARCH -->
    <div class="bg-white rounded-xl shadow mb-5">

        <form method="GET" class="p-4">

            <div class="relative max-w-md">

                <input
                    type="text"
                    name="search"
                    value="<?= $_GET['search'] ?? '' ?>"
                    placeholder="Cari nomor transaksi / barang..."
                    class="w-full border rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">

                <svg
                    class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-5.2-5.2M11 19a8 8 0 100-16 8 8 0 000 16z" />

                </svg>

            </div>

        </form>

    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            No
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            No. Transaksi
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Tanggal
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Barang
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Qty Keluar
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Tujuan
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Keterangan
                        </th>

                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    <?php if (!empty($barangKeluar)) : ?>

                        <?php foreach ($barangKeluar as $row) : ?>
                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4"><?= $no++ ?></td>

                                <td class="px-6 py-4 font-medium">
                                    <?= $row['nomor_transaksi'] ?>
                                </td>

                                <td class="px-6 py-4">
                                    <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-medium">
                                        <?= $row['nama_barang'] ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= $row['kode_barang'] ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                                        -<?= $row['jumlah'] ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <?= $row['tujuan'] ?>
                                </td>

                                <td class="px-6 py-4">
                                    <?= $row['keterangan'] ?: '-' ?>
                                </td>

                                <td>

                                    <div class="flex items-center justify-center gap-2">

                                        <!-- Edit -->
                                        <button
                                            type="button"
                                            @click="$store.modal.show({
                                            title: 'Edit Data',
                                            template: 'modal-barang-edit-data-barang-keluar',
                                            size: 'max-w-2xl',
                                            data: <?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>
                                            })"
                                            class="inline-flex items-center justify-center  w-24 h-8  rounded-lg bg-amber-500 hover:bg-amber-600 text-sm font-medium text-white transition">

                                            Edit

                                        </button>

                                        <!-- Hapus -->
                                        <form method="POST">

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="delete-data-barang-keluar-by-id">

                                            <input
                                                name="id"
                                                value="<?= $row['id']; ?>">

                                            <button
                                                type="button"
                                                @click="$store.alert.confirmDelete($el.form)"
                                                class="inline-flex mt-4 items-center justify-center  w-24 h-8  rounded-lg bg-red-600 hover:bg-red-700 text-sm font-medium text-white transition">

                                                Hapus

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>

                            <td colspan="8" class="py-16 text-center text-gray-500">

                                Belum ada data barang keluar.

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <!-- PAGINATION -->

        <div class="flex flex-col md:flex-row justify-between items-center gap-4 px-6 py-4 border-t">

            <div class="text-sm text-gray-500">

                Menampilkan

                <span class="font-semibold">
                    <?= count($barangKeluar) ?>
                </span>

                dari

                <span class="font-semibold">
                    <?= $pagination['total_data'] ?>
                </span>

                data

            </div>

            <div class="flex items-center gap-2">

                <?php if ($pagination['has_prev']) : ?>

                    <a
                        href="?page=<?= $pagination['page'] - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100">

                        Previous

                    </a>

                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['total_page']; $i++) : ?>

                    <?php if ($i == $pagination['page']) : ?>

                        <span class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            <?= $i ?>
                        </span>

                    <?php else : ?>

                        <a
                            href="?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-100">

                            <?= $i ?>

                        </a>

                    <?php endif; ?>

                <?php endfor; ?>

                <?php if ($pagination['has_next']) : ?>

                    <a
                        href="?page=<?= $pagination['page'] + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100">

                        Next

                    </a>

                <?php endif; ?>

            </div>

        </div>


    </div>
</div>
<template id="modal-barang-keluar-created">

    <form method="POST" class="space-y-5">

        <input
            type="hidden"
            name="action"
            value="created-data-barang-keluar">

        <!-- Nomor Transaksi -->
        <div>

            <label class="block mb-2 text-sm font-medium text-gray-700">
                Nomor Transaksi
            </label>

            <input
                type="text"
                name="nomor_transaksi"
                value="<?= $nomorTransaksi ?>"
                readonly
                class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2">

        </div>

        <!-- Barang -->
        <div>

            <label class="block mb-2 text-sm font-medium text-gray-700">
                Barang
                <span class="text-red-500">*</span>
            </label>

            <select
                name="barang_id"
                class="w-full border rounded-lg px-4 py-2"
                required>

                <option value="">-- Pilih Barang --</option>

                <?php foreach ($barang as $b) : ?>

                    <option value="<?= $b['id'] ?>">
                        <?= $b['kode_barang'] ?>
                        -
                        <?= $b['nama_barang'] ?>
                        (Stok: <?= $b['stok'] ?>)
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <!-- Tanggal & Jumlah -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>

                <label class="block mb-2 text-sm font-medium text-gray-700">
                    Tanggal
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="date"
                    name="tanggal"
                    value="<?= date('Y-m-d') ?>"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"
                    required>

            </div>

            <div>

                <label class="block mb-2 text-sm font-medium text-gray-700">
                    Jumlah Keluar
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="number"
                    name="jumlah"
                    min="1"
                    placeholder="Masukkan jumlah"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"
                    required>

            </div>

        </div>

        <!-- Tujuan -->
        <div>

            <label class="block mb-2 text-sm font-medium text-gray-700">
                Tujuan
                <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="tujuan"
                placeholder="Contoh : Ruang Guru, Laboratorium, Cabang A"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"
                required>

        </div>

        <!-- Keterangan -->
        <div>

            <label class="block mb-2 text-sm font-medium text-gray-700">
                Keterangan
            </label>

            <textarea
                name="keterangan"
                rows="4"
                placeholder="Masukkan keterangan..."
                class="w-full resize-none rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"></textarea>

        </div>

        <!-- Button -->
        <div class="flex justify-end gap-3 pt-2">

            <button
                type="button"
                @click="$store.modal.close()"
                class="px-5 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">

                Batal

            </button>

            <button
                type="submit"
                class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">

                Simpan

            </button>

        </div>

    </form>

</template>


<!-- edit -->
<template id="modal-barang-edit-data-barang-keluar">

    <form
        method="POST"
        x-data="{ form: {...$store.modal.data} }"
        class="space-y-5">

        <input
            type="hidden"
            name="action"
            value="update-data-barang-keluar">

        <input
            type="hidden"
            name="id"
            x-model="form.id">

        <!-- Nomor Transaksi -->
        <div>

            <label class="block mb-2 text-sm font-medium text-gray-700">
                Nomor Transaksi
            </label>

            <input
                type="text"
                name="nomor_transaksi"
                x-model="form.nomor_transaksi"
                readonly
                class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2">

        </div>

        <!-- Barang -->
        <div>

            <label class="block mb-2 text-sm font-medium text-gray-700">
                Barang
                <span class="text-red-500">*</span>
            </label>

            <select
                name="barang_id"
                x-model="form.barang_id"
                class="w-full border rounded-lg px-4 py-2"
                required>

                <option value="">-- Pilih Barang --</option>

                <?php foreach ($barang as $b) : ?>

                    <option value="<?= $b['id'] ?>">
                        <?= $b['kode_barang'] ?>
                        -
                        <?= $b['nama_barang'] ?>
                        (Stok: <?= $b['stok'] ?>)
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <!-- Tanggal & Jumlah -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>

                <label class="block mb-2 text-sm font-medium text-gray-700">
                    Tanggal
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="date"
                    name="tanggal"
                    x-model="form.tanggal"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"
                    required>

            </div>

            <div>

                <label class="block mb-2 text-sm font-medium text-gray-700">
                    Jumlah Keluar
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="number"
                    name="jumlah"
                    min="1"
                    x-model="form.jumlah"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"
                    required>

            </div>

        </div>

        <!-- Tujuan -->
        <div>

            <label class="block mb-2 text-sm font-medium text-gray-700">
                Tujuan
                <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="tujuan"
                x-model="form.tujuan"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"
                required>

        </div>

        <!-- Keterangan -->
        <div>

            <label class="block mb-2 text-sm font-medium text-gray-700">
                Keterangan
            </label>

            <textarea
                name="keterangan"
                rows="4"
                x-model="form.keterangan"
                class="w-full resize-none rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"></textarea>

        </div>

        <!-- Button -->
        <div class="flex justify-end gap-3 pt-2">

            <button
                type="button"
                @click="$store.modal.close()"
                class="px-5 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">

                Batal

            </button>

            <button
                type="submit"
                class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">

                Update

            </button>

        </div>

    </form>

</template>

<?php
require_once __DIR__ . '/../Layout/footer.php';
?>