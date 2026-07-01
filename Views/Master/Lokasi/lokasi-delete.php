<?php
session_start();

require_once __DIR__ . '/../../../Controllers/LokasiController.php';

$lokasi = new LokasiController();

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID tidak valid.";
    header("Location: lokasi.php");
    exit;
}

$result = $lokasi->delete($id);

if ($result['status']) {
    $_SESSION['success'] = $result['message'];
} else {
    $_SESSION['error'] = $result['message'];
}

header("Location: lokasi.php");
exit;