<?php

require_once __DIR__ . '/../Config/Database.php';

class Kategori extends Database
{
    public function getData($limit, $offset, $search = "")
    {
        $search = "%$search%";

        $query = "
        SELECT id, nama_kategori, created_at
        FROM kategori_m
        WHERE nama_kategori LIKE ?
        ORDER BY id DESC
        LIMIT ? OFFSET ?
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sii", $search, $limit, $offset);
        $stmt->execute();

        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    public function countData($search = "")
    {
        $search = "%$search%";

        $query = "
        SELECT COUNT(*) as total
        FROM kategori_m
        WHERE nama_kategori LIKE ?
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $search);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        return $result['total'];
    }

    public function update($data)
    {
        $id = (int) $data['id'];
        $namaKategori = trim($data['nama_kategori']);

        if (empty($namaKategori)) {
            return [
                "status" => false,
                "message" => "Nama kategori wajib diisi."
            ];
        }

        // cek duplikat (kecuali dirinya sendiri)
        $cek = $this->conn->prepare("
        SELECT id FROM kategori_m 
        WHERE nama_kategori = ? AND id != ?
    ");

        $cek->bind_param("si", $namaKategori, $id);
        $cek->execute();

        if ($cek->get_result()->num_rows > 0) {
            return [
                "status" => false,
                "message" => "Kategori sudah digunakan."
            ];
        }

        $stmt = $this->conn->prepare("
        UPDATE kategori_m 
        SET nama_kategori = ?
        WHERE id = ?
    ");

        $stmt->bind_param("si", $namaKategori, $id);

        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Kategori berhasil diupdate."
            ];
        }

        return [
            "status" => false,
            "message" => "Gagal update kategori."
        ];
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("
        SELECT * FROM kategori_m WHERE id = ?
    ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }


    public function create($data)
    {
        $namaKategori = trim($data['nama_kategori']);

        // Validasi
        if (empty($namaKategori)) {

            return [
                "status" => false,
                "message" => "Nama kategori wajib diisi."
            ];
        }

        // Cek apakah kategori sudah ada
        $cek = $this->conn->prepare("
            SELECT id
            FROM kategori_m
            WHERE nama_kategori = ?
        ");

        $cek->bind_param("s", $namaKategori);
        $cek->execute();

        if ($cek->get_result()->num_rows > 0) {

            return [
                "status" => false,
                "message" => "Kategori sudah ada."
            ];
        }

        $createdBy = $_SESSION['id'];

        $stmt = $this->conn->prepare("
            INSERT INTO kategori_m
            (
                nama_kategori,
                created_by
            )
            VALUES
            (?, ?)
        ");

        $stmt->bind_param(
            "si",
            $namaKategori,
            $createdBy
        );

        if ($stmt->execute()) {

            return [
                "status" => true,
                "message" => "Kategori berhasil ditambahkan."
            ];
        }

        return [
            "status" => false,
            "message" => "Kategori gagal ditambahkan."
        ];
    }
    public function delete($id)
    {
        $stmt = $this->conn->prepare("
        DELETE FROM kategori_m 
        WHERE id = ?
    ");

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Kategori berhasil dihapus."
            ];
        }

        return [
            "status" => false,
            "message" => "Kategori gagal dihapus."
        ];
    }
}
