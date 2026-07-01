<?php
require_once __DIR__ . '/../../Helper/config.php';
require_once __DIR__ . '/../Components/modal.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Halaman yang tidak perlu login
$currentPage = basename($_SERVER['PHP_SELF']);
$publicPages = ['login.php'];

// Jika belum login dan bukan halaman login, redirect
if (!isset($_SESSION['login']) && !in_array($currentPage, $publicPages)) {
    header('Location: ' . BASE_URL . '/Src/Auth/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? '-'; ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body x-data class="bg-gray-100">

    <div class="flex">

        <?php require_once __DIR__ . '/sidebar.php'; ?>

        <div class="flex-1 lg:ml-64">

            <?php require_once __DIR__ . '/navbar.php'; ?>

            <main class="p-8">