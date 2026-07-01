<?php

$title = "Profile";
$activeMenu = "profile";

require_once "../../Helper/function.php";

$getUsert = getProfile($_SESSION['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'edit-profile') {

        $result = editProfile($_POST);

        if ($result['status']) {

            if ($result['affected_rows'] > 0) {

                set_flash(
                    'success',
                    'Berhasil!',
                    'Data berhasil diperbarui.'
                );
            } else {

                set_flash(
                    'info',
                    'Informasi',
                    'Tidak ada data yang diubah.'
                );
            }
        } else {

            set_flash(
                'error',
                'Gagal!',
                $result['message']
            );
        }

        header("Location: profile.php");
        exit;
    }
}


require_once __DIR__ . '/../Layout/header.php';
?>

<div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Cover -->
        <div class="h-48 bg-gradient-to-r from-blue-600 to-indigo-600"></div>

        <!-- Profile -->
        <div class="px-8 pb-8">

            <div class="flex flex-col md:flex-row md:items-end md:justify-between -mt-16">

                <div class="flex flex-col md:flex-row md:items-end gap-5">

                    <img
                        src="https://ui-avatars.com/api/?name=<?= urlencode($getUsert['nama_lengkap']) ?>&background=0D8ABC&color=fff&size=200"
                        alt="Profile"
                        class="w-32 h-32 rounded-full border-4 border-white shadow-lg">

                    <div class="pb-3">
                        <h1 class="text-3xl font-bold text-gray-800">
                            <?= htmlspecialchars($getUsert['nama_lengkap']) ?>
                        </h1>

                        <p class="text-gray-500 capitalize">
                            <?= htmlspecialchars($getUsert['role']) ?>
                        </p>
                    </div>

                </div>

                <div class="mt-6 md:mt-0">
                    <button @click="$store.modal.show({
                        title: 'Edit Profile',
                        template: 'modal-edit-profile',
                        size: 'max-w-2xl'})"
                        class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition">
                        Edit Profile
                    </button>
                </div>

            </div>


            <div class="mt-10 bg-white border rounded-xl p-6">
                <h2 class="text-xl font-semibold mb-6">
                    Informasi Akun
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm text-gray-500">
                            Nama Lengkap
                        </label>
                        <p class="mt-1 font-medium">
                            <?= htmlspecialchars($getUsert['nama_lengkap']) ?>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-500">
                            Username
                        </label>
                        <p class="mt-1 font-medium">
                            <?= htmlspecialchars($getUsert['username']) ?>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-500">
                            Role
                        </label>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-medium capitalize">
                            <?= htmlspecialchars($getUsert['role']) ?>
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-500">
                            Bergabung Sejak
                        </label>
                        <p class="mt-1 font-medium">
                            <?= date('d F Y', strtotime($getUsert['created_at'])) ?>
                        </p>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

<!-- modal edit profile -->
<template id="modal-edit-profile">
    <form method="POST" x-data="{
    changePassword: false,
    showPassword: false,
    showConfirmPassword: false
}">

        <input
            type="hidden"
            name="action"
            value="edit-profile">

        <div class="space-y-4">

            <!-- Nama Lengkap -->
            <div>
                <label class="block mb-1 font-medium">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    name="nama_lengkap"
                    value="<?= htmlspecialchars($getUsert['nama_lengkap']) ?>"
                    class="w-full border rounded-lg p-3">
            </div>

            <!-- Username -->
            <div>
                <label class="block mb-1 font-medium">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    value="<?= htmlspecialchars($getUsert['username']) ?>"
                    class="w-full border rounded-lg p-3">
            </div>

            <!-- Checkbox -->
            <div class="flex items-center gap-3">

                <input
                    id="changePassword"
                    type="checkbox"
                    x-model="changePassword"
                    name="change_password"
                    value="1"
                    class="w-4 h-4">

                <label
                    for="changePassword"
                    class="cursor-pointer">

                    Ubah Password

                </label>

            </div>

            <!-- Password Baru -->
            <div
                x-show="changePassword"
                x-transition
                x-cloak>

                <label class="block mb-1 font-medium">
                    Password Baru
                </label>

                <div class="relative">

                    <input
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        class="w-full border rounded-lg p-3 pr-12">

                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-600">

                        <!-- Eye -->
                        <svg x-show="!showPassword"
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5
                            c4.478 0 8.268 2.943 9.542 7
                            -1.274 4.057-5.064 7-9.542 7
                            -4.477 0-8.268-2.943-9.542-7z" />

                        </svg>

                        <!-- Eye Off -->
                        <svg x-show="showPassword"
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19
                            c-4.478 0-8.268-2.943-9.542-7
                            a9.956 9.956 0 012.223-3.592" />

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6.228 6.228A9.956 9.956 0 0112 5
                            c4.478 0 8.268 2.943 9.542 7
                            a9.97 9.97 0 01-4.132 5.411" />

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3l18 18" />

                        </svg>

                    </button>

                </div>

            </div>

            <!-- Konfirmasi Password -->
            <div
                x-show="changePassword"
                x-transition
                x-cloak>

                <label class="block mb-1 font-medium">
                    Konfirmasi Password
                </label>

                <div class="relative">

                    <input
                        :type="showConfirmPassword ? 'text' : 'password'"
                        name="confirm_password"
                        class="w-full border rounded-lg p-3 pr-12">

                    <button
                        type="button"
                        @click="showConfirmPassword = !showConfirmPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-600">

                        <!-- Eye -->
                        <svg x-show="!showConfirmPassword"
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5
                            c4.478 0 8.268 2.943 9.542 7
                            -1.274 4.057-5.064 7-9.542 7
                            -4.477 0-8.268-2.943-9.542-7z" />

                        </svg>

                        <!-- Eye Off -->
                        <svg x-show="showConfirmPassword"
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19
                            c-4.478 0-8.268-2.943-9.542-7
                            a9.956 9.956 0 012.223-3.592" />

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6.228 6.228A9.956 9.956 0 0112 5
                            c4.478 0 8.268 2.943 9.542 7
                            a9.97 9.97 0 01-4.132 5.411" />

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3l18 18" />

                        </svg>

                    </button>

                </div>

            </div>

            <!-- Tombol -->
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