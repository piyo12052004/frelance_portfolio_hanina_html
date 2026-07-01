<?php
require_once __DIR__ . '/../Config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Dashboard
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Total master barang
    public function getTotalBarang()
    {
        $query = mysqli_query(
            $this->conn,
            "SELECT COUNT(*) AS total FROM barang_t"
        );

        return mysqli_fetch_assoc($query)['total'];
    }

    // Total transaksi barang masuk
    public function getTotalBarangMasuk()
    {
        $query = mysqli_query(
            $this->conn,
            "SELECT IFNULL(SUM(jumlah),0) AS total
             FROM barang_masuk_t"
        );

        return mysqli_fetch_assoc($query)['total'];
    }

    // Total transaksi barang keluar
    public function getTotalBarangKeluar()
    {
        $query = mysqli_query(
            $this->conn,
            "SELECT IFNULL(SUM(jumlah),0) AS total
             FROM barang_keluar_t"
        );

        return mysqli_fetch_assoc($query)['total'];
    }

    // Total stok tersedia
    public function getTotalStok()
    {
        $query = mysqli_query(
            $this->conn,
            "SELECT IFNULL(SUM(stok),0) AS total
             FROM barang_t"
        );

        return mysqli_fetch_assoc($query)['total'];
    }
}

$dashboard = new Dashboard($conn);