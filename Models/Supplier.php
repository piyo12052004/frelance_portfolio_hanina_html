<?php

require_once __DIR__ . '/../Config/Database.php';

class Supplier extends Database
{
    // =========================
    // GET DATA + PAGINATION + SEARCH
    // =========================
    public function getData($limit, $offset, $search = "")
    {
        $search = "%$search%";

        $query = "
            SELECT id, nama_supplier, alamat, telepon, email, created_at
            FROM supplier_m
            WHERE nama_supplier LIKE ?
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
            FROM supplier_m
            WHERE nama_supplier LIKE ?
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
        $nama = trim($data['nama_supplier']);
        $alamat = trim($data['alamat'] ?? '');
        $telepon = trim($data['telepon'] ?? '');
        $email = trim($data['email'] ?? '');

        if (empty($nama)) {
            return [
                "status" => false,
                "message" => "Nama supplier wajib diisi."
            ];
        }

        // cek duplikat
        $cek = $this->conn->prepare("
            SELECT id FROM supplier_m WHERE nama_supplier = ?
        ");

        $cek->bind_param("s", $nama);
        $cek->execute();

        if ($cek->get_result()->num_rows > 0) {
            return [
                "status" => false,
                "message" => "Supplier sudah ada."
            ];
        }

        $createdBy = $_SESSION['id'] ?? null;

        $stmt = $this->conn->prepare("
            INSERT INTO supplier_m (nama_supplier, alamat, telepon, email, created_by)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssssi", $nama, $alamat, $telepon, $email, $createdBy);

        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Supplier berhasil ditambahkan."
            ];
        }

        return [
            "status" => false,
            "message" => "Gagal menambahkan supplier."
        ];
    }

    // =========================
    // GET BY ID
    // =========================
    public function getById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM supplier_m WHERE id = ?
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
        $nama = trim($data['nama_supplier']);
        $alamat = trim($data['alamat'] ?? '');
        $telepon = trim($data['telepon'] ?? '');
        $email = trim($data['email'] ?? '');

        if (empty($nama)) {
            return [
                "status" => false,
                "message" => "Nama supplier wajib diisi."
            ];
        }

        $stmt = $this->conn->prepare("
            UPDATE supplier_m
            SET nama_supplier = ?, alamat = ?, telepon = ?, email = ?
            WHERE id = ?
        ");

        $stmt->bind_param("ssssi", $nama, $alamat, $telepon, $email, $id);

        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Supplier berhasil diupdate."
            ];
        }

        return [
            "status" => false,
            "message" => "Gagal update supplier."
        ];
    }

    // =========================
    // DELETE
    // =========================
    public function delete($id)
    {
        $stmt = $this->conn->prepare("
            DELETE FROM supplier_m WHERE id = ?
        ");

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Supplier berhasil dihapus."
            ];
        }

        return [
            "status" => false,
            "message" => "Gagal hapus supplier."
        ];
    }

    public function getAll()
    {
        $result = $this->conn->query("SELECT id, nama_supplier FROM supplier_m ORDER BY nama_supplier ASC");

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}
