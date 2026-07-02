<?php

require_once __DIR__ . '/../Config/Database.php';

class BarangMasuk extends Database
{
    public function getData($page = 1, $limit = 10, $search = "")
    {
        $page   = max(1, (int)$page);
        $limit  = (int)$limit;
        $offset = ($page - 1) * $limit;

        $search = $this->conn->real_escape_string($search);

        // ======================
        // WHERE
        // ======================
        $where = "WHERE 1=1";

        if (!empty($search)) {
            $where .= "
                AND (
                    b.kode_barang LIKE '%$search%'
                    OR b.nama_barang LIKE '%$search%'
                    OR s.nama_supplier LIKE '%$search%'
                )
            ";
        }

        // ======================
        // COUNT
        // ======================
        $countQuery = "
            SELECT COUNT(*) AS total
            FROM barang_masuk_t bm
            INNER JOIN barang_t b ON b.id = bm.barang_id
            LEFT JOIN supplier_m s ON s.id = bm.supplier_id
            $where
        ";

        $countResult = $this->conn->query($countQuery);
        $totalData = $countResult->fetch_assoc()['total'] ?? 0;
        $totalPage = ceil($totalData / $limit);

        // ======================
        // DATA
        // ======================
        $query = "
            SELECT
                bm.*,
                b.kode_barang,
                b.nama_barang,
                s.nama_supplier
            FROM barang_masuk_t bm
            INNER JOIN barang_t b ON b.id = bm.barang_id
            LEFT JOIN supplier_m s ON s.id = bm.supplier_id
            $where
            ORDER BY bm.id DESC
            LIMIT $limit OFFSET $offset
        ";

        $result = $this->conn->query($query);

        $rows = [];

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return [
            'data' => $rows,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'offset' => $offset,
                'total_data' => $totalData,
                'total_page' => $totalPage,
                'has_prev' => $page > 1,
                'has_next' => $page < $totalPage
            ]
        ];
    }

    public function create($data)
    {
        // session_start();

        $barang_id   = (int)($data['barang_id'] ?? 0);
        $supplier_id = (int)($data['supplier_id'] ?? 0);
        $tanggal     = $this->conn->real_escape_string(trim($data['tanggal'] ?? ''));
        $jumlah      = (int)($data['jumlah'] ?? 0);
        $harga       = ($data['harga'] === '' || !isset($data['harga'])) ? 0 : (float)$data['harga'];
        $keterangan  = $this->conn->real_escape_string(trim($data['keterangan'] ?? ''));
        $created_by  = $_SESSION['id'] ?? null;

        // ======================
        // VALIDASI
        // ======================
        if ($barang_id <= 0) {
            return ['status' => false, 'message' => 'Barang harus dipilih.'];
        }

        if ($tanggal == '') {
            return ['status' => false, 'message' => 'Tanggal wajib diisi.'];
        }

        if ($jumlah <= 0) {
            return ['status' => false, 'message' => 'Jumlah harus lebih dari 0.'];
        }

        // cek barang
        $cek = $this->conn->query("SELECT id FROM barang_t WHERE id = $barang_id LIMIT 1");

        if ($cek->num_rows == 0) {
            return ['status' => false, 'message' => 'Barang tidak ditemukan.'];
        }

        // ======================
        // TRANSACTION
        // ======================
        $this->conn->begin_transaction();

        try {

            $supplierSql = $supplier_id > 0 ? $supplier_id : "NULL";
            $createdBySql = $created_by ? $created_by : "NULL";

            // INSERT barang masuk
            $insert = "
                INSERT INTO barang_masuk_t (
                    barang_id,
                    supplier_id,
                    tanggal,
                    jumlah,
                    harga,
                    keterangan,
                    created_by,
                    created_at
                ) VALUES (
                    $barang_id,
                    $supplierSql,
                    '$tanggal',
                    $jumlah,
                    $harga,
                    '$keterangan',
                    $createdBySql,
                    NOW()
                )
            ";

            if (!$this->conn->query($insert)) {
                throw new Exception($this->conn->error);
            }

            // UPDATE STOK
            $update = "
                UPDATE barang_t
                SET stok = stok + $jumlah
                WHERE id = $barang_id
            ";

            if (!$this->conn->query($update)) {
                throw new Exception($this->conn->error);
            }

            $this->conn->commit();

            return [
                'status' => true,
                'message' => 'Barang masuk berhasil disimpan.'
            ];
        } catch (Exception $e) {

            $this->conn->rollback();

            return [
                'status' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ];
        }
    }

    public function getById($id)
    {
        $id = (int)$id;

        $query = "
        SELECT *
        FROM barang_masuk_t
        WHERE id = $id
        LIMIT 1
    ";

        $result = $this->conn->query($query);

        return $result->fetch_assoc();
    }

    public function update($data)
    {
        // session_start();

        $id          = (int)($data['id'] ?? 0);
        $barang_id   = (int)($data['barang_id'] ?? 0);
        $supplier_id = (int)($data['supplier_id'] ?? 0);
        $tanggal     = $this->conn->real_escape_string(trim($data['tanggal'] ?? ''));
        $jumlah      = (int)($data['jumlah'] ?? 0);
        $harga       = $data['harga'] === '' ? 0 : (float)$data['harga'];
        $keterangan  = $this->conn->real_escape_string(trim($data['keterangan'] ?? ''));
        $updated_by  = $_SESSION['id'] ?? null;

        if ($id <= 0) {
            return ['status' => false, 'message' => 'ID tidak valid'];
        }

        if ($barang_id <= 0) {
            return ['status' => false, 'message' => 'Barang wajib dipilih'];
        }

        if ($tanggal == '') {
            return ['status' => false, 'message' => 'Tanggal wajib diisi'];
        }

        if ($jumlah <= 0) {
            return ['status' => false, 'message' => 'Jumlah harus > 0'];
        }

        // ambil data lama untuk hitung stok rollback
        $old = $this->getById($id);
        if (!$old) {
            return ['status' => false, 'message' => 'Data tidak ditemukan'];
        }

        mysqli_begin_transaction($this->conn);

        try {

            // rollback stok lama
            $this->conn->query("
            UPDATE barang_t
            SET stok = stok - {$old['jumlah']}
            WHERE id = {$old['barang_id']}
        ");

            // update data barang masuk
            $this->conn->query("
            UPDATE barang_masuk_t
            SET 
                barang_id = $barang_id,
                supplier_id = " . ($supplier_id > 0 ? $supplier_id : "NULL") . ",
                tanggal = '$tanggal',
                jumlah = $jumlah,
                harga = $harga,
                keterangan = '$keterangan',
                updated_by = " . ($updated_by ?: "NULL") . "
            WHERE id = $id
        ");

            // tambah stok baru
            $this->conn->query("
            UPDATE barang_t
            SET stok = stok + $jumlah
            WHERE id = $barang_id
        ");

            mysqli_commit($this->conn);

            return [
                'status' => true,
                'message' => 'Data berhasil diupdate'
            ];
        } catch (Exception $e) {

            mysqli_rollback($this->conn);

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function delete($id)
    {
        $id = (int)$id;

        // ambil data dulu untuk rollback stok
        $data = $this->getById($id);

        if (!$data) {
            return [
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ];
        }

        mysqli_begin_transaction($this->conn);

        try {

            // rollback stok barang
            $this->conn->query("
            UPDATE barang_t
            SET stok = stok - {$data['jumlah']}
            WHERE id = {$data['barang_id']}
        ");

            // delete barang masuk
            $this->conn->query("
            DELETE FROM barang_masuk_t
            WHERE id = $id
        ");

            mysqli_commit($this->conn);

            return [
                'status' => true,
                'message' => 'Data barang masuk berhasil dihapus'
            ];
        } catch (Exception $e) {

            mysqli_rollback($this->conn);

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function exportData()
    {
        $query = "
        SELECT
            bm.*,
            b.kode_barang,
            b.nama_barang,
            s.nama_supplier
        FROM barang_masuk_t bm
        INNER JOIN barang_t b
            ON b.id = bm.barang_id
        LEFT JOIN supplier_m s
            ON s.id = bm.supplier_id
        ORDER BY bm.id DESC
    ";

        $result = $this->conn->query($query);

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}
