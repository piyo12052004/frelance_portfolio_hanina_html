<?php
session_start();

$title = "Tambah Supplier";
$activeMenu = "supplier";

require_once __DIR__ . '/../../../Controllers/SupplierController.php';

$supplierController = new SupplierController();

$result = $supplierController->create();

require_once __DIR__ . '/../../Layout/header.php';
?>

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-xl shadow-md">

        <!-- Header -->
        <div class="border-b px-8 py-6">

            <h1 class="text-2xl font-bold text-gray-800">
                Tambah Data Supplier
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Silakan isi data supplier di bawah ini.
            </p>

        </div>

        <?php if (isset($result) && !$result['status']) : ?>
            <div class="mb-5 rounded-lg bg-red-100 border border-red-300 p-4 text-red-700">
                <?= $result['message']; ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="" method="POST" class="p-8 space-y-6">

            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">
                    Nama Supplier
                </label>

                <input
                    type="text"
                    name="nama_supplier"
                    placeholder="Contoh : PT Sumber Makmur"
                    required
                    autofocus
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">
                    Alamat
                </label>

                <textarea
                    name="alamat"
                    placeholder="Alamat lengkap supplier"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 outline-none"
                    rows="3"></textarea>
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">
                    Telepon
                </label>

                <input
                    type="text"
                    name="telepon"
                    placeholder="08123456789"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="supplier@email.com"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">

                <a
                    href="supplier.php"
                    class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-100 transition">

                    Kembali

                </a>

                <button
                    type="submit"
                    name="simpan"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">

                    Simpan Data

                </button>

            </div>

        </form>

    </div>

</div>

<?php
require_once __DIR__ . '/../../Layout/footer.php';
?>