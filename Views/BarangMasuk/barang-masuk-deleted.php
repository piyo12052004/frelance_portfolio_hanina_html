<?php
session_start();

require_once __DIR__ . '/../../Controllers/BarangMasukController.php';

$controller = new BarangMasukController();

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID tidak valid.";
    header("Location: barang-masuk.php");
    exit;
}

$result = $controller->delete($id);

if ($result['status']) {
    $_SESSION['success'] = $result['message'];
} else {
    $_SESSION['error'] = $result['message'];
}

header("Location: barang-masuk.php");
exit;