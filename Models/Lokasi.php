<?php

require_once __DIR__ . '/../Config/Database.php';

class Lokasi extends Database
{
    // =========================
    // GET DATA + PAGINATION
    // =========================
    public function getData($limit, $offset, $search = "")
    {
        $search = "%$search%";

        $query = "
            SELECT id, nama_lokasi, keterangan, created_at
            FROM lokasi_m
            WHERE nama_lokasi LIKE ?
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

    // =========================
    // COUNT DATA
    // =========================
    public function countData($search = "")
    {
        $search = "%$search%";

        $query = "
        SELECT COUNT(*) as total
        FROM lokasi_m
        WHERE nama_lokasi LIKE ?
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $search);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        return $result['total'];
    }

    // =========================
    // CREATE
    // =========================
    public function create($data)
    {
        $nama = trim($data['nama_lokasi']);
        $keterangan = trim($data['keterangan']);

        if (empty($nama)) {
            return [
                "status" => false,
                "message" => "Nama lokasi wajib diisi."
            ];
        }

        $cek = $this->conn->prepare("
            SELECT id FROM lokasi_m WHERE nama_lokasi = ?
        ");

        $cek->bind_param("s", $nama);
        $cek->execute();

        if ($cek->get_result()->num_rows > 0) {
            return [
                "status" => false,
                "message" => "Lokasi sudah ada."
            ];
        }

        $createdBy = $_SESSION['id'] ?? null;

        $stmt = $this->conn->prepare("
            INSERT INTO lokasi_m (nama_lokasi, keterangan, created_by)
            VALUES (?, ?, ?)
        ");

        $stmt->bind_param("ssi", $nama, $keterangan, $createdBy);

        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Lokasi berhasil ditambahkan."
            ];
        }

        return [
            "status" => false,
            "message" => "Gagal menambahkan lokasi."
        ];
    }

    // =========================
    // GET BY ID (EDIT)
    // =========================
    public function getById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM lokasi_m WHERE id = ?
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // =========================
    // UPDATE
    // =========================
    public function update($data)
    {
        $id = (int) $data['id'];
        $nama = trim($data['nama_lokasi']);
        $keterangan = trim($data['keterangan']);

        if (empty($nama)) {
            return [
                "status" => false,
                "message" => "Nama lokasi wajib diisi."
            ];
        }

        $stmt = $this->conn->prepare("
            UPDATE lokasi_m
            SET nama_lokasi = ?, keterangan = ?
            WHERE id = ?
        ");

        $stmt->bind_param("ssi", $nama, $keterangan, $id);

        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Lokasi berhasil diupdate."
            ];
        }

        return [
            "status" => false,
            "message" => "Gagal update lokasi."
        ];
    }

    // =========================
    // DELETE
    // =========================
    public function delete($id)
    {
        $stmt = $this->conn->prepare("
            DELETE FROM lokasi_m WHERE id = ?
        ");

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Lokasi berhasil dihapus."
            ];
        }

        return [
            "status" => false,
            "message" => "Gagal hapus lokasi."
        ];
    }
}
