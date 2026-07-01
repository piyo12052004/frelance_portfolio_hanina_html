<?php
$showMenu = isset($_GET['menu']) && $_GET['menu'] === 'profile';
?>

<header class="bg-white shadow-md h-16 flex items-center justify-between px-8">

    <h1 class="text-2xl font-bold text-gray-700"><?= $title ?></h1>

    <div class="relative">

        <a href="?menu=profile" class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100">
            <!-- Icon -->
            👤
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