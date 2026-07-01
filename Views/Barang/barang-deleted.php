<?php
session_start();

require_once __DIR__ . '/../../Controllers/BarangController.php';

$controller = new BarangController();

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID tidak valid.";
    header("Location: barang.php");
    exit;
}

// panggil controller delete
$controller->delete($id);