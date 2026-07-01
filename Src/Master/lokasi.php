<?php

$title = "Lokasi";
$activeMenu = "lokasi";

require_once "../../Helper/function_lokasi.php";

$result = getDataLokasi($_GET);
$lokasi = $result['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    switch ($_POST['action'] ?? '') {

        case 'created-data-lokasi':
            $result = createdDataLokasi($_POST);
            break;

        case 'delete-data-lokasi':
            $result = deleteDataLokasi($_POST);
            break;

        case 'lokasi-edit-lokasi':
            $result = updateDataLokasi($_POST);
            break;

        default:
            $result = null;
    }

    if ($result) {

        set_flash(
            $result['status'] ? 'success' : 'error',
            $result['status'] ? 'Berhasil!' : 'Gagal!',
            $result['message']
        );

        header("Location: lokasi.php");
        exit;
    }
}

require_once __DIR__ . '/../Layout/header.php';
?>

<div class="bg-white rounded-xl shadow-md">

    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Data Lokasi
            </h1>

            <p class="text-sm text-gray-500">
                Kelola seluruh lokasi aset barang.
            </p>
        </div>

        <button
            @click="$store.modal.show({
        title: 'Tambah Data',
        template: 'modal-master-created-lokasi',
        size: 'max-w-2xl'
    })"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">

            + Tambah Lokasi

        </button>

    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 p-6">

            <form method="GET" class="w-full md:w-80">

                <div class="relative">

                    <input
                        type="text"
                        name="search"
                        value="<?= htmlspecialchars($result['search']) ?>"
                        placeholder="Cari lokasi..."
                        class="w-full rounded-lg border border-gray-300 pl-11 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                    <svg
                        class="absolute left-3 top-3 w-5 h-5 text-gray-400"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z" />

                    </svg>

                </div>

            </form>

        </div>
        <table class="min-w-full">

            <thead class="bg-gray-100">

                <tr>

                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                        No
                    </th>

                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                        Nama Lokasi
                    </th>

                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                        Keterangan
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                        Created At
                    </th>

                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-gray-200">

                <?php if (!empty($lokasi)) : ?>

                    <?php
                    $no = (($result['page'] - 1) * $result['limit']) + 1;
                    ?>

                    <?php foreach ($lokasi as $row) : ?>

                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <?= $no++; ?>
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800">
                                <?= htmlspecialchars($row['nama_lokasi']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= $row['keterangan'] ?: '-' ?>
                            </td>

                            <td class="px-6 py-4 text-gray-500">
                                <?= date('d M Y H:i', strtotime($row['created_at'])); ?>
                            </td>

                            <td>

                                <div class="flex items-center justify-center gap-2">

                                    <button
                                        type="button"
                                        @click="$store.modal.show({
                                        title: 'Edit Data',
                                        template: 'modal-lokasi-edit-lokasi',
                                        size: 'max-w-2xl',
                                       data: <?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>})"
                                        class="inline-flex items-center justify-center  w-24 h-8  rounded-lg bg-amber-500 text-sm font-medium text-white hover:bg-amber-600 transition">

                                        Edit

                                    </button>

                                    <form method="POST">

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="delete-data-lokasi">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $row['id']; ?>">

                                        <button
                                            type="button"
                                            @click="$store.alert.confirmDelete($el.form)"
                                            class="inline-flex items-center justify-center  w-24 h-8  mt-4 rounded-lg bg-red-600 text-sm font-medium text-white hover:bg-red-700 transition">

                                            Hapus

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else : ?>

                    <tr>

                        <td colspan="4" class="py-10 text-center text-gray-500">

                            Belum ada data Lokasi.

                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

        <div class="flex items-center justify-between px-6 py-4 border-t">

            <span class="text-sm text-gray-600">
                Total <?= $result['total']; ?> data
            </span>

            <div class="flex gap-2">

                <?php for ($i = 1; $i <= $result['total_page']; $i++) : ?>

                    <a
                        href="?page=<?= $i ?>&search=<?= urlencode($result['search']) ?>"
                        class="px-3 py-2 rounded-lg border <?= $i == $result['page']
                                                                ? 'bg-blue-600 text-white'
                                                                : 'hover:bg-gray-100' ?>">

                        <?= $i ?>

                    </a>

                <?php endfor; ?>

            </div>

        </div>

    </div>

</div>

<!-- Modal created -->
<template id="modal-master-created-lokasi">

    <form method="POST" class="space-y-5">

        <input
            type="hidden"
            name="action"
            value="created-data-lokasi">

        <div>

            <label class="block mb-2 text-sm font-medium text-gray-700">
                Nama Lokasi
            </label>

            <input
                type="text"
                name="nama_lokasi"
                placeholder="Masukkan nama lokasi"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"
                required>

        </div>

        <div>

            <label class="block mb-2 text-sm font-medium text-gray-700">
                Keterangan
            </label>

            <textarea
                name="keterangan"
                rows="4"
                placeholder="Masukkan keterangan"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 resize-none focus:ring-2 focus:ring-blue-500"></textarea>

        </div>

        <div class="flex justify-end gap-3">

            <button
                type="button"
                @click="$store.modal.close()"
                class="px-4 py-2 border rounded-lg">

                Batal

            </button>

            <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg">

                Simpan

            </button>

        </div>

    </form>

</template>

<template id="modal-lokasi-edit-lokasi">

    <form
        method="POST"
        x-data="{ form:{...$store.modal.data} }"
        class="space-y-5">

        <input
            type="hidden"
            name="action"
            value="lokasi-edit-lokasi">

        <input
            type="hidden"
            name="id"
            x-model="form.id">

        <div>

            <label class="block mb-2 text-sm font-medium">
                Nama Lokasi
            </label>

            <input
                type="text"
                name="nama_lokasi"
                x-model="form.nama_lokasi"
                class="w-full rounded-lg border border-gray-300 px-4 py-2"
                required>

        </div>

        <div>

            <label class="block mb-2 text-sm font-medium">
                Keterangan
            </label>

            <textarea
                name="keterangan"
                rows="4"
                x-model="form.keterangan"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 resize-none"></textarea>

        </div>

        <div class="flex justify-end gap-3">

            <button
                type="button"
                @click="$store.modal.close()"
                class="px-4 py-2 border rounded-lg">

                Batal

            </button>

            <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg">

                Update

            </button>

        </div>

    </form>

</template>

<?php
require_once __DIR__ . '/../Layout/footer.php';
?>