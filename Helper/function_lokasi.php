<?php
require_once __DIR__ . '/../Config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================
// FLASH MESSAGE
// ==========================
function set_flash($type, $title, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'title' => $title,
        'message' => $message
    ];
}

// ==========================
// DELETE LOKASI
// ==========================
function deleteDataLokasi($data)
{
    global $conn;

    $id = (int)($data['id'] ?? 0);

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM lokasi_m WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $affected = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

    return [
        'status' => $affected > 0,
        'affected_rows' => $affected,
        'message' => $affected > 0
            ? 'Data lokasi berhasil dihapus.'
            : 'Data lokasi gagal dihapus.'
    ];
}

// ==========================
// GET DATA LOKASI
// ==========================
function getDataLokasi($data = [])
{
    global $conn;

    $limit = 10;

    $page = isset($data['page']) && is_numeric($data['page'])
        ? (int)$data['page']
        : 1;

    if ($page < 1) {
        $page = 1;
    }

    $search = trim($data['search'] ?? '');

    $offset = ($page - 1) * $limit;

    $where = '';
    $params = [];
    $types = '';

    if ($search !== '') {
        $where = "WHERE nama_lokasi LIKE ?";
        $params[] = "%{$search}%";
        $types .= "s";
    }

    // Total Data
    $sqlTotal = "SELECT COUNT(*) AS total
                 FROM lokasi_m
                 $where";

    $stmt = mysqli_prepare($conn, $sqlTotal);

    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);

    $total = mysqli_stmt_get_result($stmt)->fetch_assoc()['total'];

    mysqli_stmt_close($stmt);

    // Ambil Data
    $sql = "
        SELECT *
        FROM lokasi_m
        $where
        ORDER BY id DESC
        LIMIT ?, ?
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if ($params) {

        $bind = array_merge($params, [$offset, $limit]);

        mysqli_stmt_bind_param(
            $stmt,
            $types . "ii",
            ...$bind
        );
    } else {

        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $offset,
            $limit
        );
    }

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $rows = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return [
        'data' => $rows,
        'search' => $search,
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_page' => ceil($total / $limit)
    ];
}

// ==========================
// CREATE LOKASI
// ==========================
function createdDataLokasi($data)
{
    global $conn;

    $namaLokasi = trim($data['nama_lokasi'] ?? '');
    $keterangan = trim($data['keterangan'] ?? '');

    if ($namaLokasi === '') {
        return [
            'status' => false,
            'affected_rows' => 0,
            'message' => 'Nama lokasi wajib diisi.'
        ];
    }

    // Cek duplikat
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id
         FROM lokasi_m
         WHERE nama_lokasi = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "s", $namaLokasi);

    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {

        mysqli_stmt_close($stmt);

        return [
            'status' => false,
            'affected_rows' => 0,
            'message' => 'Nama lokasi sudah digunakan.'
        ];
    }

    mysqli_stmt_close($stmt);

    // Insert
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO lokasi_m
        (nama_lokasi, keterangan)
        VALUES (?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $namaLokasi,
        $keterangan
    );

    $status = mysqli_stmt_execute($stmt);

    $affected = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

    return [
        'status' => $status,
        'affected_rows' => $affected,
        'message' => $status
            ? 'Data lokasi berhasil ditambahkan.'
            : mysqli_error($conn)
    ];
}

// ==========================
// UPDATE LOKASI
// ==========================
function updateDataLokasi($data)
{
    global $conn;

    $id = (int)($data['id'] ?? 0);
    $namaLokasi = trim($data['nama_lokasi'] ?? '');
    $keterangan = trim($data['keterangan'] ?? '');

    if ($id <= 0) {
        return [
            'status' => false,
            'affected_rows' => 0,
            'message' => 'ID lokasi tidak valid.'
        ];
    }

    if ($namaLokasi === '') {
        return [
            'status' => false,
            'affected_rows' => 0,
            'message' => 'Nama lokasi wajib diisi.'
        ];
    }

    // Cek duplikat
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id
         FROM lokasi_m
         WHERE nama_lokasi = ?
         AND id != ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "si",
        $namaLokasi,
        $id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        mysqli_stmt_close($stmt);

        return [
            'status' => false,
            'affected_rows' => 0,
            'message' => 'Nama lokasi sudah digunakan.'
        ];
    }

    mysqli_stmt_close($stmt);

    // Update
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE lokasi_m
        SET
            nama_lokasi = ?,
            keterangan = ?,
            updated_at = NOW()
        WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssi",
        $namaLokasi,
        $keterangan,
        $id
    );

    mysqli_stmt_execute($stmt);

    $affected = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

    return [
        'status' => true,
        'affected_rows' => $affected,
        'message' => $affected > 0
            ? 'Data lokasi berhasil diperbarui.'
            : 'Tidak ada data yang diubah.'
    ];
}
