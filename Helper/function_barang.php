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

function getDataBarang($data)
{
    global $conn;

    $page   = isset($data['page']) ? (int)$data['page'] : 1;
    $limit  = isset($data['limit']) ? (int)$data['limit'] : 10;
    $search = isset($data['search']) ? trim($data['search']) : '';

    $offset = ($page - 1) * $limit;

    $where = "WHERE 1=1";

    if ($search !== '') {
        $search = mysqli_real_escape_string($conn, $search);
        $where .= " AND (b.nama_barang LIKE '%$search%' 
                    OR b.kode_barang LIKE '%$search%')";
    }

    // TOTAL DATA
    $countQuery = "
        SELECT COUNT(*) as total
        FROM barang_t b
        LEFT JOIN kategori_m k ON k.id = b.kategori_id
        LEFT JOIN lokasi_m l ON l.id = b.lokasi_id
        $where
    ";

    $totalData = mysqli_fetch_assoc(mysqli_query($conn, $countQuery))['total'];
    $totalPage = ceil($totalData / $limit);

    // DATA
    $query = "
        SELECT 
            b.*,
            k.nama_kategori,
            l.nama_lokasi
        FROM barang_t b
        LEFT JOIN kategori_m k ON k.id = b.kategori_id
        LEFT JOIN lokasi_m l ON l.id = b.lokasi_id
        $where
        ORDER BY b.id DESC
        LIMIT $limit OFFSET $offset
    ";

    $res = mysqli_query($conn, $query);

    $dataBarang = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $dataBarang[] = $row;
    }

    return [
        'data' => $dataBarang,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total_data' => $totalData,
            'total_page' => $totalPage,
            'has_next' => $page < $totalPage,
            'has_prev' => $page > 1,
        ]
    ];
}

function getKodeBarang()
{
    global $conn;

    // ambil kode terakhir
    $query = "SELECT kode_barang 
              FROM barang_t 
              ORDER BY id DESC 
              LIMIT 1";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // jika belum ada data
    if (!$row) {
        return "AST-001";
    }

    // ambil angka dari kode terakhir
    $lastKode = $row['kode_barang'];

    // ambil angka saja (AST-001 -> 001)
    $number = (int) substr($lastKode, 4);

    // tambah 1
    $nextNumber = $number + 1;

    // format jadi 3 digit
    $newKode = "AST-" . str_pad($nextNumber, 3, "0", STR_PAD_LEFT);

    return $newKode;
}

function getDataLokasiDanKategori()
{
    global $conn;

    // ======================
    // AMBIL KATEGORI
    // ======================
    $kategoriQuery = "SELECT id, nama_kategori FROM kategori_m ORDER BY nama_kategori ASC";
    $kategoriResult = mysqli_query($conn, $kategoriQuery);

    $kategori = [];
    while ($row = mysqli_fetch_assoc($kategoriResult)) {
        $kategori[] = $row;
    }

    // ======================
    // AMBIL LOKASI
    // ======================
    $lokasiQuery = "SELECT id, nama_lokasi FROM lokasi_m ORDER BY nama_lokasi ASC";
    $lokasiResult = mysqli_query($conn, $lokasiQuery);

    $lokasi = [];
    while ($row = mysqli_fetch_assoc($lokasiResult)) {
        $lokasi[] = $row;
    }

    // ======================
    // RETURN DATA
    // ======================
    return [
        'kategori' => $kategori,
        'lokasi'   => $lokasi
    ];
}

function createdDataBarang($data)
{
    global $conn;

    $kode_barang = mysqli_real_escape_string($conn, $data['kode_barang'] ?? '');
    $nama_barang = mysqli_real_escape_string($conn, $data['nama_barang'] ?? '');
    $kategori_id = (int) ($data['kategori_id'] ?? 0);
    $lokasi_id   = (int) ($data['lokasi_id'] ?? 0);
    $merk        = mysqli_real_escape_string($conn, $data['merk'] ?? '');
    $tipe        = mysqli_real_escape_string($conn, $data['tipe'] ?? '');
    $kondisi     = mysqli_real_escape_string($conn, $data['kondisi'] ?? '');
    $stok        = (int) ($data['stok'] ?? 0);
    $created_by = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;

    if ($created_by === 0) {
        return [
            'status' => false,
            'message' => 'Session login tidak ditemukan.'
        ];
    }
    if ($stok < 0) {
        return [
            'status' => false,
            'message' => 'Stok tidak boleh kurang dari 0.'
        ];
    }

    $cek = mysqli_query($conn, "
        SELECT id
        FROM barang_t
        WHERE kode_barang = '$kode_barang'
    ");

    if (mysqli_num_rows($cek) > 0) {
        return [
            'status' => false,
            'message' => 'Kode barang sudah digunakan.'
        ];
    }
    // Validasi wajib
    if (
        empty($kode_barang) ||
        empty($nama_barang) ||
        $kategori_id == 0 ||
        $lokasi_id == 0 ||
        empty($kondisi)
    ) {
        return [
            'status' => false,
            'message' => 'Semua data wajib harus diisi.'
        ];
    }

    // Validasi tahun
    $tahun = trim($data['tahun'] ?? '');

    if ($tahun === '') {
        $tahunSql = "NULL";
    } elseif (!is_numeric($tahun) || $tahun < 1901 || $tahun > 2155) {
        return [
            'status' => false,
            'message' => 'Tahun harus antara 1901 - 2155.'
        ];
    } else {
        $tahunSql = (int)$tahun;
    }

    $query = "
        INSERT INTO barang_t (
            kode_barang,
            nama_barang,
            kategori_id,
            lokasi_id,
            merk,
            tipe,
            tahun,
            kondisi,
            stok,
            created_by,
            created_at
        ) VALUES (
            '$kode_barang',
            '$nama_barang',
            $kategori_id,
            $lokasi_id,
            '$merk',
            '$tipe',
            $tahunSql,
            '$kondisi',
            $stok,
             $created_by,
            NOW()
        )
    ";

    if (mysqli_query($conn, $query)) {
        return [
            'status' => true,
            'message' => 'Data barang berhasil ditambahkan.'
        ];
    }

    return [
        'status' => false,
        'message' => mysqli_error($conn)
    ];
}

function updateDataBarang($data)
{
    global $conn;

    $id           = (int)$data['id'];

    $kode_barang  = mysqli_real_escape_string($conn, trim($data['kode_barang']));
    $nama_barang  = mysqli_real_escape_string($conn, trim($data['nama_barang']));
    $kategori_id  = (int)$data['kategori_id'];
    $lokasi_id    = (int)$data['lokasi_id'];
    $merk         = mysqli_real_escape_string($conn, trim($data['merk']));
    $tipe         = mysqli_real_escape_string($conn, trim($data['tipe']));
    $kondisi      = mysqli_real_escape_string($conn, trim($data['kondisi']));
    $stok         = (int)$data['stok'];

    $updated_by = (int)$_SESSION['id'];

    // ==========================
    // Validasi
    // ==========================

    if (
        $id <= 0 ||
        empty($nama_barang) ||
        $kategori_id <= 0 ||
        $lokasi_id <= 0 ||
        empty($kondisi)
    ) {
        return [
            'status' => false,
            'message' => 'Data belum lengkap.'
        ];
    }

    // ==========================
    // Validasi Tahun
    // ==========================

    $tahun = trim($data['tahun']);

    if ($tahun == '') {

        $tahunSql = "NULL";

    } elseif (!is_numeric($tahun) || $tahun < 1901 || $tahun > 2155) {

        return [
            'status' => false,
            'message' => 'Tahun tidak valid.'
        ];

    } else {

        $tahunSql = (int)$tahun;

    }

    // ==========================
    // Cek kode barang
    // ==========================

    $cek = mysqli_query(
        $conn,
        "SELECT id
        FROM barang_t
        WHERE kode_barang='$kode_barang'
        AND id<>$id"
    );

    if (mysqli_num_rows($cek) > 0) {

        return [
            'status' => false,
            'message' => 'Kode barang sudah digunakan.'
        ];

    }

    // ==========================
    // Ambil foto lama
    // ==========================

    $queryFoto = mysqli_query(
        $conn,
        "SELECT foto
        FROM barang_t
        WHERE id=$id"
    );

    $oldFoto = mysqli_fetch_assoc($queryFoto)['foto'] ?? null;

    $foto = $oldFoto;

    // ==========================
    // Upload Foto Baru
    // ==========================

    if (
        isset($_FILES['foto']) &&
        $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE
    ) {

        $file = $_FILES['foto'];

        if ($file['size'] > 2 * 1024 * 1024) {

            return [
                'status' => false,
                'message' => 'Ukuran foto maksimal 2 MB.'
            ];

        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        $ext = strtolower(pathinfo(
            $file['name'],
            PATHINFO_EXTENSION
        ));

        if (!in_array($ext, $allowed)) {

            return [
                'status' => false,
                'message' => 'Format foto harus JPG, JPEG, PNG atau WEBP.'
            ];

        }

        $uploadDir = __DIR__ . '/../Uploads/barang/';

        if (!is_dir($uploadDir)) {

            mkdir($uploadDir, 0777, true);

        }

        $newFoto = uniqid('barang_', true) . "." . $ext;

        if (
            !move_uploaded_file(
                $file['tmp_name'],
                $uploadDir . $newFoto
            )
        ) {

            return [
                'status' => false,
                'message' => 'Upload foto gagal.'
            ];

        }

        // Hapus foto lama
        if (
            !empty($oldFoto) &&
            file_exists($uploadDir . $oldFoto)
        ) {
            unlink($uploadDir . $oldFoto);
        }

        $foto = $newFoto;
    }

    // ==========================
    // Update
    // ==========================

    $query = "
        UPDATE barang_t SET
            nama_barang='$nama_barang',
            kategori_id=$kategori_id,
            lokasi_id=$lokasi_id,
            merk='$merk',
            tipe='$tipe',
            tahun=$tahunSql,
            kondisi='$kondisi',
            stok=$stok,
            foto=" . ($foto ? "'$foto'" : "NULL") . ",
            updated_by=$updated_by,
            updated_at=NOW()
        WHERE id=$id
    ";

    if (mysqli_query($conn, $query)) {

        return [
            'status' => true,
            'message' => 'Data barang berhasil diperbarui.'
        ];

    }

    return [
        'status' => false,
        'message' => mysqli_error($conn)
    ];
}

// function deleteDataBarang($data){
//     tolong hapus data barang
// }
