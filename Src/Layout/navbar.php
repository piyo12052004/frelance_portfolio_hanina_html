<header class="bg-white shadow-md h-16 flex items-center justify-between px-8">

    <div>
        <h1 class="text-2xl font-bold text-gray-700">
            <?= $title ?>
        </h1>
    </div>

    <div class="relative" x-data="{ open: false }">

        <button
            @click="open = !open"
            class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 transition focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-gray-700">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
        </button>

        <div
            x-show="open"
            @click.outside="open = false"
            x-transition
            class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden z-50">

            <a
                href="<?= BASE_URL ?>/Src/Profile/profile.php"
                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                Profile
            </a>

            <?php if (!empty($_SESSION['login'])) : ?>
                <a
                    href="<?= BASE_URL; ?>/Src/Layout/logout.php"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition border-t border-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Logout
                </a>

            <?php else : ?>
                <a
                    href="<?= BASE_URL ?>/Src/Auth/login.php"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-blue-600 hover:bg-blue-50 transition border-t border-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>

                    Login
                </a>
            <?php endif; ?>

        </div>

    </div>

</header>