<?php
require_once __DIR__ . '/../Config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// alert
function set_flash($type, $title, $message)
{
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'error', 'warning', 'info'
        'title' => $title,
        'message' => $message
    ];
}

function getDataBarangKeluar($data)
{
    global $conn;

    // ======================
    // PARAMETER
    // ======================
    $page   = max(1, (int)($data['page'] ?? 1));
    $limit  = max(1, (int)($data['limit'] ?? 10));
    $search = trim($data['search'] ?? '');

    $offset = ($page - 1) * $limit;

    // ======================
    // WHERE
    // ======================
    $where = "WHERE 1=1";

    if ($search != '') {

        $search = mysqli_real_escape_string($conn, $search);

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
        SELECT COUNT(*) total
        FROM barang_keluar_t bk
        INNER JOIN barang_t b
            ON b.id = bk.barang_id
        $where
    ";

    $countResult = mysqli_query($conn, $countQuery);

    $totalData = mysqli_fetch_assoc($countResult)['total'];

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
        INNER JOIN barang_t b
            ON b.id = bk.barang_id
        $where
        ORDER BY bk.id DESC
        LIMIT $limit OFFSET $offset
    ";

    $result = mysqli_query($conn, $query);

    $barangKeluar = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $barangKeluar[] = $row;
    }

    // ======================
    // RETURN
    // ======================
    return [
        'data' => $barangKeluar,

        'pagination' => [
            'page'        => $page,
            'limit'       => $limit,
            'offset'      => $offset,
            'total_data'  => $totalData,
            'total_page'  => $totalPage,
            'has_prev'    => $page > 1,
            'has_next'    => $page < $totalPage,
        ]
    ];
}

function getNomorTransaksiBarangKeluar()
{
    global $conn;

    $prefix = "BK-" . date('Ymd') . "-";

    // Ambil nomor transaksi terakhir hari ini
    $query = "
        SELECT nomor_transaksi
        FROM barang_keluar_t
        WHERE nomor_transaksi LIKE '{$prefix}%'
        ORDER BY id DESC
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    // Jika query gagal
    if (!$result) {
        return [
            'status'  => false,
            'message' => mysqli_error($conn),
            'data'    => null
        ];
    }

    // Belum ada transaksi hari ini
    if (mysqli_num_rows($result) == 0) {
        return [
            'status'  => true,
            'message' => 'Nomor transaksi berhasil dibuat.',
            'data'    => $prefix . "0001"
        ];
    }

    $row = mysqli_fetch_assoc($result);

    // Ambil angka terakhir
    $lastNumber = (int) substr($row['nomor_transaksi'], -4);

    // Tambah 1
    $newNumber = $lastNumber + 1;

    return [
        'status'  => true,
        'message' => 'Nomor transaksi berhasil dibuat.',
        'data'    => $prefix . str_pad($newNumber, 4, "0", STR_PAD_LEFT)
    ];
}

function getDataBarangForBarangKeluar()
{
    global $conn;

    $query = "
        SELECT
            id,
            kode_barang,
            nama_barang,
            stok
        FROM barang_t
        WHERE stok > 0
        ORDER BY nama_barang ASC
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        return [
            'status'  => false,
            'message' => mysqli_error($conn),
            'data'    => []
        ];
    }

    $barang = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $barang[] = $row;
    }

    return [
        'status'  => true,
        'message' => 'Data barang berhasil diambil.',
        'data'    => $barang
    ];
}

function createdDataBarangKeluar($data)
{
    global $conn;

    $nomor_transaksi = mysqli_real_escape_string($conn, trim($data['nomor_transaksi'] ?? ''));
    $barang_id       = (int) ($data['barang_id'] ?? 0);
    $tanggal         = mysqli_real_escape_string($conn, $data['tanggal'] ?? '');
    $jumlah          = (int) ($data['jumlah'] ?? 0);
    $tujuan          = mysqli_real_escape_string($conn, trim($data['tujuan'] ?? ''));
    $keterangan      = mysqli_real_escape_string($conn, trim($data['keterangan'] ?? ''));
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
    // CEK STOK BARANG
    // ======================

    $cekBarang = mysqli_query($conn, "
        SELECT stok
        FROM barang_t
        WHERE id = $barang_id
        LIMIT 1
    ");

    if (!$cekBarang || mysqli_num_rows($cekBarang) == 0) {
        return [
            'status' => false,
            'message' => 'Barang tidak ditemukan.'
        ];
    }

    $barang = mysqli_fetch_assoc($cekBarang);

    if ($barang['stok'] < $jumlah) {
        return [
            'status' => false,
            'message' => 'Stok tidak mencukupi. Stok tersedia : ' . $barang['stok']
        ];
    }

    mysqli_begin_transaction($conn);

    try {

        // ======================
        // INSERT BARANG KELUAR
        // ======================

        $insert = mysqli_query($conn, "
            INSERT INTO barang_keluar_t
            (
                nomor_transaksi,
                barang_id,
                tanggal,
                jumlah,
                tujuan,
                keterangan,
                created_by,
                created_at
            )
            VALUES
            (
                '$nomor_transaksi',
                $barang_id,
                '$tanggal',
                $jumlah,
                '$tujuan',
                '$keterangan',
                $created_by,
                NOW()
            )
        ");

        if (!$insert) {
            throw new Exception(mysqli_error($conn));
        }

        // ======================
        // KURANGI STOK
        // ======================

        $updateStok = mysqli_query($conn, "
            UPDATE barang_t
            SET stok = stok - $jumlah
            WHERE id = $barang_id
        ");

        if (!$updateStok) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_commit($conn);

        return [
            'status' => true,
            'message' => 'Barang keluar berhasil disimpan.'
        ];
    } catch (Exception $e) {

        mysqli_rollback($conn);

        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
}

function updateDataBarangKeluar($data)
{
    global $conn;

    $id               = (int) ($data['id'] ?? 0);
    $nomor_transaksi  = mysqli_real_escape_string($conn, trim($data['nomor_transaksi'] ?? ''));
    $barang_id        = (int) ($data['barang_id'] ?? 0);
    $tanggal          = mysqli_real_escape_string($conn, $data['tanggal'] ?? '');
    $jumlah           = (int) ($data['jumlah'] ?? 0);
    $tujuan           = mysqli_real_escape_string($conn, trim($data['tujuan'] ?? ''));
    $keterangan       = mysqli_real_escape_string($conn, trim($data['keterangan'] ?? ''));
    $updated_by       = $_SESSION['id'] ?? null;

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

    $oldQuery = mysqli_query($conn, "
        SELECT barang_id, jumlah
        FROM barang_keluar_t
        WHERE id = $id
        LIMIT 1
    ");

    if (!$oldQuery || mysqli_num_rows($oldQuery) == 0) {
        return [
            'status' => false,
            'message' => 'Data tidak ditemukan.'
        ];
    }

    $old = mysqli_fetch_assoc($oldQuery);

    mysqli_begin_transaction($conn);

    try {

        // =====================
        // KEMBALIKAN STOK LAMA
        // =====================

        $restore = mysqli_query($conn, "
            UPDATE barang_t
            SET stok = stok + {$old['jumlah']}
            WHERE id = {$old['barang_id']}
        ");

        if (!$restore) {
            throw new Exception(mysqli_error($conn));
        }

        // =====================
        // CEK STOK TERBARU
        // =====================

        $cekBarang = mysqli_query($conn, "
            SELECT stok
            FROM barang_t
            WHERE id = $barang_id
            LIMIT 1
        ");

        if (!$cekBarang || mysqli_num_rows($cekBarang) == 0) {
            throw new Exception("Barang tidak ditemukan.");
        }

        $barang = mysqli_fetch_assoc($cekBarang);

        if ($barang['stok'] < $jumlah) {
            throw new Exception("Stok tidak mencukupi. Stok tersedia : {$barang['stok']}");
        }

        // =====================
        // UPDATE DATA
        // =====================

        $update = mysqli_query($conn, "
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
            throw new Exception(mysqli_error($conn));
        }

        // =====================
        // KURANGI STOK BARU
        // =====================

        $updateStok = mysqli_query($conn, "
            UPDATE barang_t
            SET stok = stok - $jumlah
            WHERE id = $barang_id
        ");

        if (!$updateStok) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_commit($conn);

        return [
            'status' => true,
            'message' => 'Data barang keluar berhasil diperbarui.'
        ];
    } catch (Exception $e) {

        mysqli_rollback($conn);

        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
}

function deleteDataBarangKeluar($id)
{
    global $conn;

    $id = (int)$id;
    
    if ($id <= 0) {
        return [
            'status' => false,
            'message' => 'ID tidak valid.'
        ];
    }

    mysqli_begin_transaction($conn);

    try {

        // Ambil data transaksi
        $query = mysqli_query($conn, "
            SELECT barang_id, jumlah
            FROM barang_keluar_t
            WHERE id = $id
            LIMIT 1
        ");

        if (mysqli_num_rows($query) == 0) {
            throw new Exception("Data barang keluar tidak ditemukan.");
        }

        $data = mysqli_fetch_assoc($query);

        $barangId = (int)$data['barang_id'];
        $jumlah   = (int)$data['jumlah'];

        // Kembalikan stok
        $updateStok = mysqli_query($conn, "
            UPDATE barang_t
            SET stok = stok + $jumlah
            WHERE id = $barangId
        ");

        if (!$updateStok) {
            throw new Exception(mysqli_error($conn));
        }

        // Hapus transaksi
        $delete = mysqli_query($conn, "
            DELETE FROM barang_keluar_t
            WHERE id = $id
        ");

        if (!$delete) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_commit($conn);

        return [
            'status' => true,
            'message' => 'Data barang keluar berhasil dihapus.'
        ];
    } catch (Exception $e) {

        mysqli_rollback($conn);

        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
}
