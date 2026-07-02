<?php
$showMenu = isset($_GET['menu']) && $_GET['menu'] === 'profile';
?>

<header class="bg-white shadow-md h-16 flex items-center justify-between px-8">

    <h1 class="text-2xl font-bold text-gray-700"><?= $title ?></h1>

    <div class="relative">

        <a href="?menu=profile" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">

            <!-- Icon -->
            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-7 h-7 text-gray-600">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </div>

            <!-- User Info -->
            <div class="text-left hidden md:block">
                <p class="font-semibold text-gray-800">
                    <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Guest') ?>
                </p>

                <p class="text-xs text-gray-500">
                    <?= htmlspecialchars($_SESSION['role'] ?? '-') ?>
                </p>
            </div>

        </a>

        <?php if ($showMenu): ?>
            <div class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg border">

                <?php if (!empty($_SESSION['login'])): ?>
                    <a href="<?= BASE_URL ?>/Views/Auth/logout.php"
                        class="block px-4 py-3 text-red-600 hover:bg-red-100">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/Views/Auth/login.php"
                        class="block px-4 py-3 text-blue-600 hover:bg-blue-100">
                        Login
                    </a>
                <?php endif; ?>

                <a href="<?= strtok($_SERVER['REQUEST_URI'], '?') ?>"
                    class="block px-4 py-3 text-center border-t">
                    Tutup
                </a>

            </div>
        <?php endif; ?>

    </div>

</header>