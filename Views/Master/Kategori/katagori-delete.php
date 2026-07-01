<?php
session_start();

require_once __DIR__ . '/../../../Controllers/KategoriController.php';

$kategori = new KategoriController();

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID tidak valid.";
    header("Location: katagori.php");
    exit;
}

$kategori->delete($id);