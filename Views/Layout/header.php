<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Helper/config.php';
require_once __DIR__ . '/../../Models/User.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$publicPages = ['login.php'];
if (!isset($_SESSION['login']) && !in_array($currentPage, $publicPages)) {
    header('Location: ' . BASE_URL . '/Views/Auth/login.php');
    exit;
}
if (
    !isset($_SESSION['login']) &&
    isset($_COOKIE['remember_login'])
) {

    $userModel = new User();

    $user = $userModel->getUserById($_COOKIE['remember_login']);

    if ($user) {

        $_SESSION['login'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="flex">

        <?php require_once __DIR__ . '/sedbar.php'; ?>

        <div class="flex-1 lg:ml-64">

            <?php require_once __DIR__ . '/navbar.php'; ?>

            <main class="p-8">