<?php

require_once __DIR__ . '/../Config/Database.php';

class BarangKeluar extends Database
{
    public function getData($page = 1, $limit = 10, $search = '')
    {
        $page   = max(1, (int)$page);
        $limit  = max(1, (int)$limit);
        $offset = ($page - 1) * $limit;

        $search = $this->conn->real_escape_string(trim($search));

        $where = "WHERE 1=1";

        if ($search !== '') {
            $where .= "
                AND (
                    bk.nomor_transaksi LIKE '%$search%'
                    OR b.kode_barang LIKE '%$search%'
                    OR b.nama_barang LIKE '%$search%'
                    OR bk.tujuan LIKE '%$search%'
                )
            ";
        }

        // ======================
        // TOTAL DATA
        // ======================
        $countQuery = "
            SELECT COUNT(*) as total
            FROM barang_keluar_t bk
            INNER JOIN barang_t b ON b.id = bk.barang_id
            $where
        ";

        $countResult = $this->conn->query($countQuery);
        $totalData = $countResult->fetch_assoc()['total'];
        $totalPage = max(1, ceil($totalData / $limit));

        // ======================
        // DATA
        // ======================
        $query = "
            SELECT
                bk.*,
                b.kode_barang,
                b.nama_barang,
                b.merk,
                b.tipe
            FROM barang_keluar_t bk
            INNER JOIN barang_t b ON b.id = bk.barang_id
            $where
            ORDER BY bk.id DESC
            LIMIT $limit OFFSET $offset
        ";

        $result = $this->conn->query($query);

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return [
            'data' => $data,
            'pagination' => [
                'page'       => $page,
                'limit'      => $limit,
                'offset'     => $offset,
                'total_data' => $totalData,
                'total_page' => $totalPage,
                'has_prev'   => $page > 1,
                'has_next'   => $page < $totalPage,
            ]
        ];
    }

    public function create($data)
    {
        // session_start();

        $nomor_transaksi = $this->conn->real_escape_string(trim($data['nomor_transaksi'] ?? ''));
        $barang_id       = (int) ($data['barang_id'] ?? 0);
        $tanggal         = $this->conn->real_escape_string($data['tanggal'] ?? '');
        $jumlah          = (int) ($data['jumlah'] ?? 0);
        $tujuan          = $this->conn->real_escape_string(trim($data['tujuan'] ?? ''));
        $keterangan      = $this->conn->real_escape_string(trim($data['keterangan'] ?? ''));
        $created_by      = $_SESSION['id'] ?? null;

        // ======================
        // VALIDASI
        // ======================
        if (
            empty($nomor_transaksi) ||
            $barang_id <= 0 ||
            empty($tanggal) ||
            $jumlah <= 0 ||
            empty($tujuan)
        ) {
            return [
                'status' => false,
                'message' => 'Semua field wajib harus diisi.'
            ];
        }

        // ======================
        // CEK BARANG & STOK
        // ======================
        $cek = $this->conn->query("
            SELECT stok FROM barang_t WHERE id = $barang_id LIMIT 1
        ");

        if (!$cek || $cek->num_rows == 0) {
            return [
                'status' => false,
                'message' => 'Barang tidak ditemukan.'
            ];
        }

        $barang = $cek->fetch_assoc();

        if ($barang['stok'] < $jumlah) {
            return [
                'status' => false,
                'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $barang['stok']
            ];
        }

        mysqli_begin_transaction($this->conn);

        try {

            // INSERT BARANG KELUAR
            $this->conn->query("
                INSERT INTO barang_keluar_t (
                    nomor_transaksi,
                    barang_id,
                    tanggal,
                    jumlah,
                    tujuan,
                    keterangan,
                    created_by,
                    created_at
                )
                VALUES (
                    '$nomor_transaksi',
                    $barang_id,
                    '$tanggal',
                    $jumlah,
                    '$tujuan',
                    '$keterangan',
                    " . ($created_by ?: "NULL") . ",
                    NOW()
                )
            ");

            // KURANGI STOK
            $this->conn->query("
                UPDATE barang_t
                SET stok = stok - $jumlah
                WHERE id = $barang_id
            ");

            mysqli_commit($this->conn);

            return [
                'status' => true,
                'message' => 'Barang keluar berhasil disimpan.'
            ];
        } catch (Exception $e) {

            mysqli_rollback($this->conn);

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getNomorTransaksi()
    {
        $prefix = "BK-" . date('Ymd') . "-";

        $query = "
            SELECT nomor_transaksi
            FROM barang_keluar_t
            WHERE nomor_transaksi LIKE '{$prefix}%'
            ORDER BY id DESC
            LIMIT 1
        ";

        $result = $this->conn->query($query);

        if (!$result) {
            return $prefix . "0001";
        }

        if ($result->num_rows == 0) {
            return $prefix . "0001";
        }

        $row = $result->fetch_assoc();

        $lastNumber = (int) substr($row['nomor_transaksi'], -4);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 4, "0", STR_PAD_LEFT);
    }

    // public function getBarang()
    // {
    //     $query = "SELECT id, nama_barang, kode_barang FROM barang_t ORDER BY nama_barang ASC";
    //     $result = $this->conn->query($query);

    //     $data = [];
    //     while ($row = $result->fetch_assoc()) {
    //         $data[] = $row;
    //     }

    //     return $data;
    // }

    public function findById($id)
    {
        $id = (int)$id;

        $query = "
        SELECT *
        FROM barang_keluar_t
        WHERE id = $id
        LIMIT 1
    ";

        $result = $this->conn->query($query);

        return $result->fetch_assoc();
    }

    public function update($data)
    {
        $id              = (int) ($data['id'] ?? 0);
        $nomor_transaksi = $this->conn->real_escape_string(trim($data['nomor_transaksi'] ?? ''));
        $barang_id       = (int) ($data['barang_id'] ?? 0);
        $tanggal         = $this->conn->real_escape_string($data['tanggal'] ?? '');
        $jumlah          = (int) ($data['jumlah'] ?? 0);
        $tujuan          = $this->conn->real_escape_string(trim($data['tujuan'] ?? ''));
        $keterangan      = $this->conn->real_escape_string(trim($data['keterangan'] ?? ''));
        $updated_by      = $_SESSION['id'] ?? null;

        // =====================
        // VALIDASI
        // =====================
        if (
            $id <= 0 ||
            empty($nomor_transaksi) ||
            $barang_id <= 0 ||
            empty($tanggal) ||
            $jumlah <= 0 ||
            empty($tujuan)
        ) {
            return [
                'status' => false,
                'message' => 'Semua field wajib harus diisi.'
            ];
        }

        // =====================
        // AMBIL DATA LAMA
        // =====================
        $oldResult = $this->conn->query("
        SELECT barang_id, jumlah
        FROM barang_keluar_t
        WHERE id = $id
        LIMIT 1
    ");

        if (!$oldResult || $oldResult->num_rows == 0) {
            return [
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ];
        }

        $old = $oldResult->fetch_assoc();

        // =====================
        // TRANSAKSI
        // =====================
        $this->conn->begin_transaction();

        try {

            // =====================
            // KEMBALIKAN STOK LAMA
            // =====================
            $restore = $this->conn->query("
            UPDATE barang_t
            SET stok = stok + {$old['jumlah']}
            WHERE id = {$old['barang_id']}
        ");

            if (!$restore) {
                throw new Exception($this->conn->error);
            }

            // =====================
            // CEK STOK BARU
            // =====================
            $cek = $this->conn->query("
            SELECT stok
            FROM barang_t
            WHERE id = $barang_id
            LIMIT 1
        ");

            if (!$cek || $cek->num_rows == 0) {
                throw new Exception("Barang tidak ditemukan.");
            }

            $barang = $cek->fetch_assoc();

            if ($barang['stok'] < $jumlah) {
                throw new Exception("Stok tidak mencukupi. Stok tersedia: {$barang['stok']}");
            }

            // =====================
            // UPDATE BARANG KELUAR
            // =====================
            $update = $this->conn->query("
            UPDATE barang_keluar_t
            SET
                nomor_transaksi = '$nomor_transaksi',
                barang_id = $barang_id,
                tanggal = '$tanggal',
                jumlah = $jumlah,
                tujuan = '$tujuan',
                keterangan = '$keterangan',
                updated_by = $updated_by,
                updated_at = NOW()
            WHERE id = $id
        ");

            if (!$update) {
                throw new Exception($this->conn->error);
            }

            // =====================
            // KURANGI STOK BARU
            // =====================
            $decrease = $this->conn->query("
            UPDATE barang_t
            SET stok = stok - $jumlah
            WHERE id = $barang_id
        ");

            if (!$decrease) {
                throw new Exception($this->conn->error);
            }

            $this->conn->commit();

            return [
                'status' => true,
                'message' => 'Data barang keluar berhasil diperbarui.'
            ];
        } catch (Exception $e) {

            $this->conn->rollback();

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function delete($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return [
                'status' => false,
                'message' => 'ID tidak valid.'
            ];
        }

        mysqli_begin_transaction($this->conn);

        try {

            // ======================
            // AMBIL DATA TRANSAKSI
            // ======================
            $query = $this->conn->query("
            SELECT barang_id, jumlah
            FROM barang_keluar_t
            WHERE id = $id
            LIMIT 1
        ");

            if ($query->num_rows == 0) {
                throw new Exception("Data barang keluar tidak ditemukan.");
            }

            $data = $query->fetch_assoc();

            $barangId = (int)$data['barang_id'];
            $jumlah   = (int)$data['jumlah'];

            // ======================
            // KEMBALIKAN STOK
            // ======================
            $updateStok = $this->conn->query("
            UPDATE barang_t
            SET stok = stok + $jumlah
            WHERE id = $barangId
        ");

            if (!$updateStok) {
                throw new Exception($this->conn->error);
            }

            // ======================
            // HAPUS DATA
            // ======================
            $delete = $this->conn->query("
            DELETE FROM barang_keluar_t
            WHERE id = $id
        ");

            if (!$delete) {
                throw new Exception($this->conn->error);
            }

            mysqli_commit($this->conn);

            return [
                'status' => true,
                'message' => 'Data barang keluar berhasil dihapus.'
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
            bk.*,
            b.kode_barang,
            b.nama_barang
        FROM barang_keluar_t bk
        INNER JOIN barang_t b
            ON b.id = bk.barang_id
        ORDER BY bk.id DESC
    ";

        $result = $this->conn->query($query);

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}
