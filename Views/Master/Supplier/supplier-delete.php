<?php
session_start();

require_once __DIR__ . '/../../../Controllers/SupplierController.php';

$supplier = new SupplierController();

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID tidak valid.";
    header("Location: supplier.php");
    exit;
}

$result = $supplier->delete($id);

if ($result['status']) {
    $_SESSION['success'] = $result['message'];
} else {
    $_SESSION['error'] = $result['message'];
}

header("Location: supplier.php");
exit;