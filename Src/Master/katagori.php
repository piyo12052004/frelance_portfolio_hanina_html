<?php

$title = "Kategori";
$activeMenu = "kategori";

require_once "../../Helper/function_kategori.php";

$result = getDataKategori($_GET);
$kategori = $result['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    switch ($_POST['action'] ?? '') {

        case 'created-data-kategori':

            $result = createdDataKategori($_POST);

            break;

        case 'delete-data-kategori':

            $result = deleteDataKategori($_POST);

            break;

        case 'kategori-edit-kategori':
            $result = updateDataKategori($_POST);
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

        header("Location: katagori.php");
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
                Data Kategori
            </h1>

            <p class="text-sm text-gray-500">
                Kelola seluruh kategori aset barang.
            </p>
        </div>

        <button
            @click="$store.modal.show({
        title: 'Tambah Data',
        template: 'modal-master-created-katagori',
        size: 'max-w-2xl'
    })"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">

            + Tambah Kategori

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
                        placeholder="Cari kategori..."
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
                        Nama Kategori
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

                <?php if (!empty($kategori)) : ?>

                    <?php
                    $no = (($result['page'] - 1) * $result['limit']) + 1;
                    ?>

                    <?php foreach ($kategori as $row) : ?>

                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <?= $no++; ?>
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800">
                                <?= htmlspecialchars($row['nama_kategori']); ?>
                            </td>

                            <td class="px-6 py-4 text-gray-500">
                                <?= date('d M Y H:i', strtotime($row['created_at'])); ?>
                            </td>

                            <td class="px-6 py-4">

                                <div class="flex items-center justify-center gap-2">

                                    <button
                                        type="button"
                                        @click="$store.modal.show({
                                        title: 'Edit Data',
                                        template: 'modal-kategori-edit-kategori',
                                        size: 'max-w-2xl',
                                       data: <?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>})"
                                        class="inline-flex items-center justify-center w-24 h-10 rounded-lg bg-amber-500 text-sm font-medium text-white hover:bg-amber-600 transition">

                                        Edit

                                    </button>

                                    <form method="POST">

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="delete-data-kategori">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $row['id']; ?>">

                                        <button
                                            type="button"
                                            @click="$store.alert.confirmDelete($el.form)"
                                            class="inline-flex items-center justify-center w-24 h-10 mt-4 rounded-lg bg-red-600 text-sm font-medium text-white hover:bg-red-700 transition">

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

                            Belum ada data kategori.

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
<template id="modal-master-created-katagori">

    <form
        method="POST"
        class="space-y-5">
        <input
            type="hidden"
            name="action"
            value="created-data-kategori">
        <div>

            <label class="block mb-2 text-sm font-medium text-gray-700">
                Nama Kategori
            </label>

            <input
                type="text"
                name="nama_kategori"
                placeholder="Masukkan nama kategori"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required>

        </div>

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

                Simpan

            </button>

        </div>

    </form>

</template>

<template id="modal-kategori-edit-kategori">

    <form method="POST" x-data="{ form: {...$store.modal.data} }">
        <input
            type="hidden"
            name="action"
            value="kategori-edit-kategori">

        <input type="hidden"
            name="id"
            x-model="form.id">

        <div class="space-y-4">

            <div>
                <label>Nama Lengkap</label>

                <input
                    name="nama_kategori"
                    x-model="form.nama_kategori"
                    class="w-full border p-2 rounded">
            </div>


            <div class="flex justify-end">

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                    Edit

                </button>

            </div>

        </div>

    </form>

</template>

<?php
require_once __DIR__ . '/../Layout/footer.php';
?>