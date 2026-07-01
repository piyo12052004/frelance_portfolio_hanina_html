<?php
$title = "Tambah Lokasi";
$activeMenu = "lokasi";

require_once __DIR__ . '/../../../Controllers/LokasiController.php';

$lokasiController = new LokasiController();

$result = $lokasiController->create();

require_once __DIR__ . '/../../Layout/header.php';
?>

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-xl shadow-md">

        <!-- Header -->
        <div class="border-b px-8 py-6">

            <h1 class="text-2xl font-bold text-gray-800">
                Tambah Data Lokasi
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Silakan isi data lokasi di bawah ini.
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
                <label
                    for="nama_lokasi"
                    class="block mb-2 text-sm font-semibold text-gray-700">

                    Nama Lokasi
                </label>

                <input
                    type="text"
                    id="nama_lokasi"
                    name="nama_lokasi"
                    placeholder="Contoh : Gudang A"
                    required
                    autofocus
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label
                    for="keterangan"
                    class="block mb-2 text-sm font-semibold text-gray-700">

                    Keterangan
                </label>

                <textarea
                    id="keterangan"
                    name="keterangan"
                    placeholder="Deskripsi lokasi"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 outline-none"
                    rows="4"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">

                <a
                    href="lokasi.php"
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