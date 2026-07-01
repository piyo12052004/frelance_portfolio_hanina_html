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

function getDataBarangMasuk($data)
{
    global $conn;

    // ======================
    // PARAMETER
    // ======================
    $page   = isset($data['page']) ? (int)$data['page'] : 1;
    $limit  = isset($data['limit']) ? (int)$data['limit'] : 10;
    $search = trim($data['search'] ?? '');

    if ($page < 1) {
        $page = 1;
    }

    $offset = ($page - 1) * $limit;

    // ======================
    // WHERE
    // ======================
    $where = "WHERE 1=1";

    if ($search != '') {

        $search = mysqli_real_escape_string($conn, $search);

        $where .= "
            AND (
                b.kode_barang LIKE '%$search%'
                OR b.nama_barang LIKE '%$search%'
                OR s.nama_supplier LIKE '%$search%'
            )
        ";
    }

    // ======================
    // TOTAL DATA
    // ======================
    $countQuery = "
        SELECT COUNT(*) AS total
        FROM barang_masuk_t bm
        INNER JOIN barang_t b
            ON b.id = bm.barang_id
        LEFT JOIN supplier_m s
            ON s.id = bm.supplier_id
        $where
    ";

    $countResult = mysqli_query($conn, $countQuery);
    $totalData   = mysqli_fetch_assoc($countResult)['total'];
    $totalPage   = ceil($totalData / $limit);

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
        INNER JOIN barang_t b
            ON b.id = bm.barang_id
        LEFT JOIN supplier_m s
            ON s.id = bm.supplier_id
        $where
        ORDER BY bm.id DESC
        LIMIT $limit OFFSET $offset
    ";

    $result = mysqli_query($conn, $query);

    $rows = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return [
        'data' => $rows,
        'pagination' => [
            'page'        => $page,
            'limit'       => $limit,
            'offset'      => $offset,
            'total_data'  => $totalData,
            'total_page'  => $totalPage,
            'has_prev'    => $page > 1,
            'has_next'    => $page < $totalPage
        ]
    ];
}

function getBarangDanSupplier()
{
    global $conn;

    // ======================
    // DATA BARANG
    // ======================
    $barang = [];

    $queryBarang = "
        SELECT
            id,
            kode_barang,
            nama_barang,
            stok
        FROM barang_t
        ORDER BY nama_barang ASC
    ";

    $resultBarang = mysqli_query($conn, $queryBarang);

    while ($row = mysqli_fetch_assoc($resultBarang)) {
        $barang[] = $row;
    }

    // ======================
    // DATA SUPPLIER
    // ======================
    $supplier = [];

    $querySupplier = "
        SELECT
            id,
            nama_supplier
        FROM supplier_m
        ORDER BY nama_supplier ASC
    ";

    $resultSupplier = mysqli_query($conn, $querySupplier);

    while ($row = mysqli_fetch_assoc($resultSupplier)) {
        $supplier[] = $row;
    }

    // ======================
    // RETURN
    // ======================
    return [
        'barang'   => $barang,
        'supplier' => $supplier
    ];
}

function createdDataBarangMasuk($data)
{
    global $conn;

    // ==========================
    // AMBIL DATA
    // ==========================
    $barang_id   = (int)($data['barang_id'] ?? 0);
    $supplier_id = (int)($data['supplier_id'] ?? 0);
    $tanggal     = mysqli_real_escape_string($conn, trim($data['tanggal'] ?? ''));
    $jumlah      = (int)($data['jumlah'] ?? 0);
    $harga       = $data['harga'] === '' ? 0 : (float)$data['harga'];
    $keterangan  = mysqli_real_escape_string($conn, trim($data['keterangan'] ?? ''));
    $created_by  = $_SESSION['id'] ?? null;

    // ==========================
    // VALIDASI
    // ==========================
    if ($barang_id <= 0) {
        return [
            'status' => false,
            'message' => 'Barang harus dipilih.'
        ];
    }

    if ($tanggal == '') {
        return [
            'status' => false,
            'message' => 'Tanggal wajib diisi.'
        ];
    }

    if ($jumlah <= 0) {
        return [
            'status' => false,
            'message' => 'Jumlah harus lebih dari 0.'
        ];
    }

    // cek barang
    $cekBarang = mysqli_query($conn, "
        SELECT id
        FROM barang_t
        WHERE id = $barang_id
        LIMIT 1
    ");

    if (mysqli_num_rows($cekBarang) == 0) {
        return [
            'status' => false,
            'message' => 'Barang tidak ditemukan.'
        ];
    }

    mysqli_begin_transaction($conn);

    try {

        // ==========================
        // INSERT BARANG MASUK
        // ==========================
        $supplierSql = $supplier_id > 0 ? $supplier_id : "NULL";
        $createdBySql = $created_by ? $created_by : "NULL";

        $insert = "
            INSERT INTO barang_masuk_t
            (
                barang_id,
                supplier_id,
                tanggal,
                jumlah,
                harga,
                keterangan,
                created_by,
                created_at
            )
            VALUES
            (
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

        if (!mysqli_query($conn, $insert)) {
            throw new Exception(mysqli_error($conn));
        }

        // ==========================
        // UPDATE STOK
        // ==========================
        $updateStok = "
            UPDATE barang_t
            SET stok = stok + $jumlah
            WHERE id = $barang_id
        ";

        if (!mysqli_query($conn, $updateStok)) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_commit($conn);

        return [
            'status' => true,
            'message' => 'Barang masuk berhasil disimpan.'
        ];
    } catch (Exception $e) {

        mysqli_rollback($conn);

        return [
            'status' => false,
            'message' => 'Gagal menyimpan data : ' . $e->getMessage()
        ];
    }
}

function updateDataBarangMasuk($data)
{
    global $conn;

    $id          = (int)($data['id'] ?? 0);
    $barang_id   = (int)($data['barang_id'] ?? 0);
    $supplier_id = !empty($data['supplier_id']) ? (int)$data['supplier_id'] : "NULL";
    $tanggal     = mysqli_real_escape_string($conn, trim($data['tanggal'] ?? ''));
    $jumlah      = (int)($data['jumlah'] ?? 0);
    $harga       = (float)($data['harga'] ?? 0);
    $keterangan  = mysqli_real_escape_string($conn, trim($data['keterangan'] ?? ''));

    // ==========================
    // VALIDASI
    // ==========================
    if (
        $id <= 0 ||
        $barang_id <= 0 ||
        empty($tanggal) ||
        $jumlah <= 0
    ) {
        return [
            'status' => false,
            'message' => 'Data wajib belum lengkap.'
        ];
    }

    // ==========================
    // DATA LAMA
    // ==========================
    $old = mysqli_query($conn, "
        SELECT barang_id, jumlah
        FROM barang_masuk_t
        WHERE id=$id
    ");

    if (mysqli_num_rows($old) == 0) {
        return [
            'status' => false,
            'message' => 'Data tidak ditemukan.'
        ];
    }

    $oldData = mysqli_fetch_assoc($old);

    mysqli_begin_transaction($conn);

    try {

        // ==========================
        // KEMBALIKAN STOK LAMA
        // ==========================
        mysqli_query($conn, "
            UPDATE barang_t
            SET stok = stok - {$oldData['jumlah']}
            WHERE id = {$oldData['barang_id']}
        ");

        // ==========================
        // TAMBAH STOK BARU
        // ==========================
        mysqli_query($conn, "
            UPDATE barang_t
            SET stok = stok + $jumlah
            WHERE id = $barang_id
        ");

        // ==========================
        // UPDATE TRANSAKSI
        // ==========================
        mysqli_query($conn, "
            UPDATE barang_masuk_t
            SET
                barang_id   = $barang_id,
                supplier_id = $supplier_id,
                tanggal     = '$tanggal',
                jumlah      = $jumlah,
                harga       = $harga,
                keterangan  = '$keterangan',
                updated_by  = {$_SESSION['id']},
                updated_at  = NOW()
            WHERE id = $id
        ");

        mysqli_commit($conn);

        return [
            'status' => true,
            'message' => 'Data barang masuk berhasil diperbarui.'
        ];
    } catch (Exception $e) {

        mysqli_rollback($conn);

        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
}

function deleteDataBarangMasuk($data)
{
    global $conn;
    
    $id = (int)($data['id'] ?? 0);

    if ($id <= 0) {
        return [
            'status' => false,
            'message' => 'ID tidak valid.'
        ];
    }

    // Ambil data barang masuk
    $query = mysqli_query($conn, "
        SELECT barang_id, jumlah
        FROM barang_masuk_t
        WHERE id = $id
    ");

    if (mysqli_num_rows($query) == 0) {
        return [
            'status' => false,
            'message' => 'Data tidak ditemukan.'
        ];
    }

    $barangMasuk = mysqli_fetch_assoc($query);

    mysqli_begin_transaction($conn);

    try {

        // Kurangi stok barang
        mysqli_query($conn, "
            UPDATE barang_t
            SET stok = stok - {$barangMasuk['jumlah']}
            WHERE id = {$barangMasuk['barang_id']}
        ");

        if (mysqli_affected_rows($conn) == 0) {
            throw new Exception("Gagal memperbarui stok barang.");
        }

        // Hapus transaksi barang masuk
        mysqli_query($conn, "
            DELETE FROM barang_masuk_t
            WHERE id = $id
        ");

        if (mysqli_affected_rows($conn) == 0) {
            throw new Exception("Gagal menghapus data.");
        }

        mysqli_commit($conn);

        return [
            'status' => true,
            'message' => 'Data barang masuk berhasil dihapus.'
        ];
    } catch (Exception $e) {

        mysqli_rollback($conn);

        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
}
