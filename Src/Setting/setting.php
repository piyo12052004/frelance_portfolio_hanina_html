<?php

$title = "Setting";
$activeMenu = "setting";

require_once "../../Helper/function.php";

$page = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? '';

$users = getUsers([
    'page' => $page,
    'limit' => 10,
    'search' => $search
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'edit-role-user') {
        $result = editUsersRoleSetting($_POST);

        if ($result['status']) {

            if ($result['affected_rows'] > 0) {

                set_flash(
                    'success',
                    'Berhasil!',
                    'Role user berhasil diperbarui.'
                );
            } else {

                set_flash(
                    'info',
                    'Informasi',
                    'Tidak ada perubahan data.'
                );
            }
        } else {

            set_flash(
                'error',
                'Gagal!',
                $result['message']
            );
        }

        header("Location: setting.php");
        exit;
    }
}

require_once __DIR__ . '/../Layout/header.php';
?>

<div class="bg-white rounded-xl shadow-md overflow-hidden">

    <div class="grid grid-cols-12 min-h-[650px]">

        <!-- Sidebar -->
        <aside class="col-span-3 border-r bg-gray-50">

            <div class="p-6 border-b">

                <h2 class="text-xl font-bold text-gray-800">
                    Pengaturan
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Kelola pengaturan aplikasi.
                </p>

            </div>

            <nav class="p-3 space-y-2">

                <a href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 bg-blue-600 text-white">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />

                    </svg>

                    Role Users

                </a>

            </nav>

        </aside>

        <!-- Content -->
        <section class="col-span-9 p-8">

            <!-- Header -->
            <div class="flex items-center justify-between">

                <div>

                    <h1 class="text-2xl font-bold text-gray-800">
                        Role Users
                    </h1>

                    <p class="text-gray-500 mt-1">
                        Kelola semua pengguna sistem role users.
                    </p>

                </div>

                <!-- <button
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                    + Tambah User

                </button> -->

            </div>

            <!-- Search -->
            <div class="mt-6">

                <form method="GET" class="mt-6">

                    <input
                        type="text"
                        name="search"
                        value="<?= htmlspecialchars($search) ?>"
                        placeholder="Cari nama atau username..."
                        class="w-full border rounded-lg px-4 py-3">

                </form>

            </div>

            <!-- Table -->
            <div class="mt-6 overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>

                        <tr class="bg-gray-100 text-gray-700">

                            <th class="px-5 py-3 text-left">
                                No
                            </th>

                            <th class="px-5 py-3 text-left">
                                Role
                            </th>

                            <th class="px-5 py-3 text-left">
                                Nama Lengkap
                            </th>

                            <th class="px-5 py-3 text-left">
                                Username
                            </th>

                            <th class="px-5 py-3 text-left">
                                Tanggal Dibuat
                            </th>

                            <th class="px-5 py-3 text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($users['data'] as $index => $user) : ?>

                            <tr class="border-b hover:bg-gray-50">

                                <td class="px-5 py-4">
                                    <?= (($users['page'] - 1) * $users['limit']) + $index + 1 ?>
                                </td>

                                <td class="px-5 py-4">

                                    <?php
                                    $color = match (strtolower($user['role'])) {
                                        'superadmin' => 'bg-red-100 text-red-700',
                                        'admin'      => 'bg-blue-100 text-blue-700',
                                        default      => 'bg-gray-100 text-gray-700'
                                    };
                                    ?>

                                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                        <?= $user['role'] ?>
                                    </span>

                                </td>

                                <td class="px-5 py-4 font-medium">
                                    <?= htmlspecialchars($user['nama_lengkap']) ?>
                                </td>

                                <td class="px-5 py-4">
                                    <?= htmlspecialchars($user['username']) ?>
                                </td>

                                <td class="px-5 py-4">
                                    <?= date('d M Y', strtotime($user['created_at'])) ?>
                                </td>

                                <td class="px-5 py-4">

                                    <div class="flex justify-center gap-2">

                                        <button @click="$store.modal.show({
                                        title: 'Edit Role Users',
                                        template: 'modal-setting-edit-role-users',
                                        size: 'max-w-2xl',
                                       data: <?= htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') ?>})"
                                            class="px-3 py-1 rounded bg-yellow-500 hover:bg-yellow-600 text-white">

                                            Edit Role

                                        </button>
                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- Footer Table -->
            <div class="mt-6 flex items-center justify-between">

                <?php
                $start = ($users['page'] - 1) * $users['limit'] + 1;
                $end = min(
                    $users['page'] * $users['limit'],
                    $users['total']
                );
                ?>

                <p class="text-sm text-gray-500">

                    Menampilkan
                    <b><?= $start ?></b>
                    -
                    <b><?= $end ?></b>

                    dari

                    <b><?= $users['total'] ?></b>
                    data

                </p>

                <!-- Pagination -->
                <div class="flex items-center gap-2">

                    <!-- Previous -->
                    <?php if ($users['page'] > 1) : ?>

                        <a
                            href="?page=<?= $users['page'] - 1 ?>&search=<?= urlencode($search) ?>"
                            class="border rounded-lg px-4 py-2 hover:bg-gray-100">

                            Previous

                        </a>

                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $users['total_pages']; $i++) : ?>

                        <a
                            href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                            class="<?= $i == $users['page']
                                        ? 'bg-blue-600 text-white'
                                        : 'border hover:bg-gray-100' ?> rounded-lg px-4 py-2">

                            <?= $i ?>

                        </a>

                    <?php endfor; ?>

                    <!-- Next -->
                    <?php if ($users['page'] < $users['total_pages']) : ?>

                        <a
                            href="?page=<?= $users['page'] + 1 ?>&search=<?= urlencode($search) ?>"
                            class="border rounded-lg px-4 py-2 hover:bg-gray-100">

                            Next

                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </section>

    </div>

</div>

<template id="modal-setting-edit-role-users">

    <form method="POST" x-data="{ form: {...$store.modal.data} }">
        <input
            type="hidden"
            name="action"
            value="edit-role-user">

        <input type="hidden"
            name="id"
            x-model="form.id">

        <div class="space-y-4">

            <div>
                <label>Nama Lengkap</label>

                <input
                    disabled
                    x-model="form.nama_lengkap"
                    class="w-full border p-2 rounded">
            </div>

            <div>
                <label>Username</label>

                <input
                    disabled
                    x-model="form.username"
                    class="w-full border p-2 rounded">
            </div>

            <div>

                <label>Role</label>

                <select
                    name="role"
                    x-model="form.role"
                    class="w-full border rounded-lg p-2">

                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                    <option value="superadmin">Superadmin</option>

                </select>

            </div>
            <div class="flex justify-end">

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                    Simpan

                </button>

            </div>

        </div>

    </form>

</template>

<?php
require_once __DIR__ . '/../Layout/footer.php';
?>