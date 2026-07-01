<?php
require_once __DIR__ . '/../Config/db.php';
// session_start();

if (!isset($_SESSION['login']) && isset($_COOKIE['remember_me'])) {

    $token = $_COOKIE['remember_me'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE remember_token = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['login'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    }
}