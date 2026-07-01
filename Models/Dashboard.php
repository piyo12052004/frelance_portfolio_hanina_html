<?php

require_once __DIR__ . '/../Config/Database.php';

class Dashboard extends Database
{
    // Total Barang
    public function getTotalBarang()
    {
        $result = $this->conn->query("
            SELECT COUNT(*) AS total
            FROM barang_t
        ");

        return $result->fetch_assoc()['total'];
    }

    // Total Kategori
    public function getTotalKategori()
    {
        $result = $this->conn->query("
            SELECT COUNT(*) AS total
            FROM kategori_m
        ");

        return $result->fetch_assoc()['total'];
    }

    // Total Barang Masuk
    public function getTotalBarangMasuk()
    {
        $result = $this->conn->query("
            SELECT COUNT(*) AS total
            FROM barang_masuk_t
        ");

        return $result->fetch_assoc()['total'];
    }

    // Total Barang Keluar
    public function getTotalBarangKeluar()
    {
        $result = $this->conn->query("
            SELECT COUNT(*) AS total
            FROM barang_keluar_t
        ");

        return $result->fetch_assoc()['total'];
    }

    // Semua data dashboard
    public function getDashboard()
    {
        return [
            'totalBarang'        => $this->getTotalBarang(),
            'totalKategori'      => $this->getTotalKategori(),
            'totalBarangMasuk'   => $this->getTotalBarangMasuk(),
            'totalBarangKeluar'  => $this->getTotalBarangKeluar(),
        ];
    }
}