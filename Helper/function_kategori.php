<?php
require_once __DIR__ . '/../Config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// alert kategori
function set_flash($type, $title, $message)
{
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'error', 'warning', 'info'
        'title' => $title,
        'message' => $message
    ];
}

function deleteDataKategori($data)
{
    global $conn;

    $id = (int) $data['id'];

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM kategori_m WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id
    );

    mysqli_stmt_execute($stmt);

    $affected = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

    return [
        'status' => $affected > 0,
        'affected_rows' => $affected,
        'message' => $affected > 0
            ? 'Data kategori berhasil dihapus.'
            : 'Data kategori gagal dihapus.'
    ];
}

function getDataKategori($data = [])
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
        $where = "WHERE nama_kategori LIKE ?";
        $params[] = "%{$search}%";
        $types .= 's';
    }

    // Total data
    $sqlTotal = "SELECT COUNT(*) as total FROM kategori_m $where";
    $stmt = mysqli_prepare($conn, $sqlTotal);

    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);

    $total = mysqli_stmt_get_result($stmt)->fetch_assoc()['total'];

    mysqli_stmt_close($stmt);

    // Data
    $sql = "SELECT *
            FROM kategori_m
            $where
            ORDER BY id DESC
            LIMIT ?, ?";

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

function createdDataKategori($data)
{
    global $conn;

    $nama_kategori = trim($data['nama_kategori'] ?? '');

    // Validasi
    if ($nama_kategori === '') {
        return [
            'status' => false,
            'affected_rows' => 0,
            'message' => 'Nama kategori wajib diisi.'
        ];
    }

    // Cek apakah sudah ada
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id FROM kategori_m WHERE nama_kategori = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "s", $nama_kategori);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);

        return [
            'status' => false,
            'affected_rows' => 0,
            'message' => 'Nama kategori sudah digunakan.'
        ];
    }

    mysqli_stmt_close($stmt);

    // Insert data
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO kategori_m (nama_kategori) VALUES (?)"
    );

    mysqli_stmt_bind_param($stmt, "s", $nama_kategori);

    $status = mysqli_stmt_execute($stmt);
    $affected_rows = mysqli_stmt_affected_rows($stmt);

    if (!$status) {
        $message = mysqli_error($conn);
    } else {
        $message = 'Berhasil';
    }

    mysqli_stmt_close($stmt);

    return [
        'status' => $status,
        'affected_rows' => $affected_rows,
        'message' => $message
    ];
}

function updateDataKategori($data)
{
    global $conn;

    $id = (int) ($data['id'] ?? 0);
    $namaKategori = trim($data['nama_kategori'] ?? '');

    // Validasi
    if ($id <= 0) {
        return [
            'status' => false,
            'affected_rows' => 0,
            'message' => 'ID kategori tidak valid.'
        ];
    }

    if ($namaKategori === '') {
        return [
            'status' => false,
            'affected_rows' => 0,
            'message' => 'Nama kategori wajib diisi.'
        ];
    }

    // Cek apakah nama kategori sudah dipakai selain data ini
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id FROM kategori_m WHERE nama_kategori = ? AND id != ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "si",
        $namaKategori,
        $id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        mysqli_stmt_close($stmt);

        return [
            'status' => false,
            'affected_rows' => 0,
            'message' => 'Nama kategori sudah digunakan.'
        ];
    }

    mysqli_stmt_close($stmt);

    // Update data
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE kategori_m
         SET nama_kategori = ?,
             updated_at = NOW()
         WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "si",
        $namaKategori,
        $id
    );

    mysqli_stmt_execute($stmt);

    $affected = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

    return [
        'status' => true,
        'affected_rows' => $affected,
        'message' => $affected > 0
            ? 'Data kategori berhasil diperbarui.'
            : 'Tidak ada data yang diubah.'
    ];
}
