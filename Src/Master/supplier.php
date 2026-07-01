<?php

$title = "Supplier";
$activeMenu = "supplier";

require_once "../../Helper/function_supplier.php";

$result = getDataSupplier($_GET);
$supplier = $result['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    switch ($_POST['action'] ?? '') {

        case 'created-data-supplier':
            $result = createdDataSupplier($_POST);
            break;

        case 'delete-data-supplier':
            $result = deleteDataSupplier($_POST);
            break;

        case 'supplier-edit-supplier':
            $result = updateDataSupplier($_POST);
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

        header("Location: supplier.php");
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
                Data Supplier
            </h1>

            <p class="text-sm text-gray-500">
                Kelola seluruh data supplier.
            </p>
        </div>

        <button
            @click="$store.modal.show({
        title: 'Tambah Data',
        template: 'modal-master-created-supplier',
        size: 'max-w-2xl'
    })"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">

            + Tambah Supplier

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
                        placeholder="Cari supplier..."
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
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="px-4 py-3 w-16 text-center text-sm font-semibold text-gray-700">
                            No
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Nama Supplier
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Alamat
                        </th>

                        <th class="px-4 py-3 w-40 text-left text-sm font-semibold text-gray-700">
                            Telepon
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Email
                        </th>

                        <th class="px-6 py-3 w-44 text-left text-sm font-semibold text-gray-700">
                            Created At
                        </th>

                        <th class="px-6 py-3 w-56 text-center text-sm font-semibold text-gray-700">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">

                    <?php if (!empty($supplier)) : ?>

                        <?php
                        $no = (($result['page'] - 1) * $result['limit']) + 1;
                        ?>

                        <?php foreach ($supplier as $row) : ?>

                            <tr class="hover:bg-blue-50 transition">

                                <td class="px-4 py-4 text-center">
                                    <?= $no++; ?>
                                </td>

                                <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                    <?= htmlspecialchars($row['nama_supplier']); ?>
                                </td>

                                <td class="px-6 py-4 text-gray-600 max-w-xs">
                                    <?= !empty($row['alamat']) ? htmlspecialchars($row['alamat']) : '-'; ?>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?= $row['telepon'] ?: '-'; ?>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= $row['email'] ?: '-'; ?>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    <?= date('d M Y H:i', strtotime($row['created_at'])); ?>
                                </td>

                                <td>

                                    <div class="flex items-center justify-center gap-2">

                                        <button
                                            type="button"
                                            @click="$store.modal.show({
                                        title: 'Edit Data',
                                        template: 'modal-supplier-edit-supplier',
                                        size: 'max-w-2xl',
                                       data: <?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>})"
                                            class="inline-flex items-center justify-center  w-24 h-8  rounded-lg bg-amber-500 text-sm font-medium text-white hover:bg-amber-600 transition">

                                            Edit

                                        </button>

                                        <form method="POST">

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="delete-data-supplier">

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

                            <td colspan="7" class="py-12 text-center text-gray-500">

                                Belum ada data supplier.

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

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
<template id="modal-master-created-supplier">

    <form method="POST" class="space-y-5">

        <input
            type="hidden"
            name="action"
            value="created-data-supplier">

        <div>

            <label>Nama Supplier</label>

            <input
                type="text"
                name="nama_supplier"
                class="w-full rounded-lg border px-4 py-2"
                required>

        </div>

        <div>

            <label>Alamat</label>

            <textarea
                name="alamat"
                rows="4"
                class="w-full rounded-lg border px-4 py-2"></textarea>

        </div>

        <div>

            <label>Telepon</label>

            <input
                type="text"
                name="telepon"
                class="w-full rounded-lg border px-4 py-2">

        </div>

        <div>

            <label>Email</label>

            <input
                type="email"
                name="email"
                class="w-full rounded-lg border px-4 py-2">

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

<template id="modal-supplier-edit-supplier">

    <form
        method="POST"
        x-data="{ form:{...$store.modal.data} }"
        class="space-y-5">

        <input
            type="hidden"
            name="action"
            value="supplier-edit-supplier">

        <input
            type="hidden"
            name="id"
            x-model="form.id">

        <div>

            <label>Nama Supplier</label>

            <input
                type="text"
                name="nama_supplier"
                x-model="form.nama_supplier"
                class="w-full rounded-lg border px-4 py-2"
                required>

        </div>

        <div>

            <label>Alamat</label>

            <textarea
                name="alamat"
                rows="4"
                x-model="form.alamat"
                class="w-full rounded-lg border px-4 py-2"></textarea>

        </div>

        <div>

            <label>Telepon</label>

            <input
                type="text"
                name="telepon"
                x-model="form.telepon"
                class="w-full rounded-lg border px-4 py-2">

        </div>

        <div>

            <label>Email</label>

            <input
                type="email"
                name="email"
                x-model="form.email"
                class="w-full rounded-lg border px-4 py-2">

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