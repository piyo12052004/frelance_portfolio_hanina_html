<?php

$title = "Dashboard";

require_once __DIR__ . '/Src/Layout/header.php';
?>

<div class="grid grid-cols-4 gap-6">

    <div class="bg-blue-500 rounded-xl p-6 text-white">

        <h2 class="text-4xl font-bold">
            10
        </h2>

        <p>
            Project
        </p>

    </div>

    <div class="bg-green-500 rounded-xl p-6 text-white">

        <h2 class="text-4xl font-bold">
            8
        </h2>

        <p>
            Skill
        </p>

    </div>

    <div class="bg-yellow-500 rounded-xl p-6 text-white">

        <h2 class="text-4xl font-bold">
            5
        </h2>

        <p>
            Certificate
        </p>

    </div>

    <div class="bg-cyan-500 rounded-xl p-6 text-white">

        <h2 class="text-4xl font-bold">
            3
        </h2>

        <p>
            Experience
        </p>

    </div>

</div>

<div class="bg-white rounded-xl shadow mt-8 p-6">

    <h2 class="text-2xl font-bold mb-5">

        Selamat Datang 👋

    </h2>

    <p class="text-gray-600">

        Ini adalah dashboard portfolio yang dibuat menggunakan PHP Native dan Tailwind CSS.

    </p>

</div>

<?php

require_once __DIR__ . '/Src/Layout/footer.php';

?>