<?php
require_once __DIR__ . '/../../Helper/function.php';

// session_start();

// ==========================
// HAPUS REMEMBER ME COOKIE
// ==========================
if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, '/');
}

// ==========================
// HAPUS TOKEN DI DATABASE (optional tapi penting)
// ==========================
if (isset($_SESSION['id'])) {
    global $conn;

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE users SET remember_token = NULL WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
    mysqli_stmt_execute($stmt);
}

// ==========================
// CLEAR SESSION
// ==========================
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

// ==========================
// FLASH MESSAGE
// ==========================
session_start();
set_flash('success', 'Logout Berhasil', 'Anda telah keluar dari sistem.');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging out...</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">

    <?php if (isset($_SESSION['flash'])) : ?>
        <script>
            Swal.fire({
                icon: "<?= $_SESSION['flash']['type']; ?>",
                title: "<?= $_SESSION['flash']['title']; ?>",
                text: "<?= $_SESSION['flash']['message']; ?>",
                timer: 2000, // Dipercepat ke 2 detik biar tidak kelamaan
                showConfirmButton: true
            }).then((result) => {
                localStorage.clear();
                sessionStorage.clear();
                window.location.href = "../Auth/login.php";
            });
        </script>
        <?php unset($_SESSION['flash']); ?>
    <?php else : ?>
        <script>
            localStorage.clear();
            sessionStorage.clear();
            window.location.href = "../Auth/login.php";
        </script>
    <?php endif; ?>

</body>

</html>