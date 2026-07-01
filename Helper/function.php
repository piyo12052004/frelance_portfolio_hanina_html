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

// function abort_404()
// {
//     // Arahkan ke file 404.php yang ada di root
//     include __DIR__ . '/../404.php';
//     exit;
// }

function register($data)
{
    global $conn;

    $errors = [];

    $nama_lengkap = trim($data['nama_lengkap'] ?? '');
    $username     = trim($data['username'] ?? '');
    $password     = $data['password'] ?? '';
    $confirm      = $data['confirm_password'] ?? '';

    // Validasi Nama
    if ($nama_lengkap == '') {
        $errors['nama_lengkap'] = "Nama lengkap wajib diisi.";
    }

    // Validasi Username
    if ($username == '') {
        $errors['username'] = "Username wajib diisi.";
    } else {

        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");

        if (mysqli_num_rows($cek) > 0) {
            $errors['username'] = "Username sudah digunakan.";
        }
    }

    // Validasi Password
    if ($password == '') {

        $errors['password'] = "Password wajib diisi.";
    } elseif (strlen($password) < 8) {

        $errors['password'] = "Password minimal 8 karakter.";
    }

    // Validasi Konfirmasi Password
    if ($confirm == '') {

        $errors['confirm_password'] = "Konfirmasi password wajib diisi.";
    } elseif ($password !== $confirm) {

        $errors['confirm_password'] = "Konfirmasi password tidak sama.";
    }

    // Kalau ada error
    if (!empty($errors)) {

        return [
            'status' => false,
            'errors' => $errors
        ];
    }

    // Hash password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Insert
    $query = "INSERT INTO users
            (nama_lengkap, username, password)
            VALUES
            ('$nama_lengkap','$username','$password')";

    if (mysqli_query($conn, $query)) {

        return [
            'status' => true
        ];
    }

    return [
        'status' => false,
        'errors' => [
            'database' => mysqli_error($conn)
        ]
    ];
}

function login($data)
{
    global $conn;

    $errors = [];

    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($username)) {
        $errors['username'] = "Username wajib diisi.";
    }

    if (empty($password)) {
        $errors['password'] = "Password wajib diisi.";
    }

    if (!empty($errors)) {
        return [
            'status' => false,
            'errors' => $errors
        ];
    }

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

    if (mysqli_num_rows($query) == 0) {
        return [
            'status' => false,
            'errors' => [
                'username' => 'Username Atau Password Salah.'
            ]
        ];
    }

    $user = mysqli_fetch_assoc($query);

    if (!password_verify($password, $user['password'])) {
        return [
            'status' => false,
            'errors' => [
                'username' => 'Username Atau Password Salah.'
            ]
        ];
    }

    $_SESSION['login'] = true;
    $_SESSION['login'] = true;
    $_SESSION['id'] = $user['id'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    return [
        'status' => true,
        'user' => [
            'id' => $user['id'],
            'nama_lengkap' => $user['nama_lengkap'],
            'username' => $user['username'],
            'role' => $user['role'],
        ]
    ];
}

function getProfile($id)
{
    global $conn;

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function editProfile($data)
{
    global $conn;

    $id = $_SESSION['id'];

    $nama_lengkap = trim($data['nama_lengkap'] ?? '');
    $username     = trim($data['username'] ?? '');

    // Validasi nama
    if ($nama_lengkap === '') {
        return [
            'status' => false,
            'message' => 'Nama lengkap wajib diisi.'
        ];
    }

    // Validasi username
    if ($username === '') {
        return [
            'status' => false,
            'message' => 'Username wajib diisi.'
        ];
    }

    // Cek username sudah digunakan user lain
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id FROM users WHERE username = ? AND id != ?"
    );

    mysqli_stmt_bind_param($stmt, "si", $username, $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        return [
            'status' => false,
            'message' => 'Username sudah digunakan.'
        ];
    }

    // ===============================
    // Ubah Password (opsional)
    // ===============================
    if (!empty($data['change_password'])) {

        $password = trim($data['password'] ?? '');
        $confirm  = trim($data['confirm_password'] ?? '');

        if ($password === '') {
            return [
                'status' => false,
                'message' => 'Password baru wajib diisi.'
            ];
        }

        if (strlen($password) < 8) {
            return [
                'status' => false,
                'message' => 'Password minimal 8 karakter.'
            ];
        }

        if ($password !== $confirm) {
            return [
                'status' => false,
                'message' => 'Konfirmasi password tidak sesuai.'
            ];
        }

        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE users
             SET
                nama_lengkap = ?,
                username = ?,
                password = ?,
                updated_by = ?,
                updated_at = NOW()
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssii",
            $nama_lengkap,
            $username,
            $password,
            $id,
            $id
        );
    } else {

        // ===============================
        // Tanpa ubah password
        // ===============================
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE users
             SET
                nama_lengkap = ?,
                username = ?,
                updated_by = ?,
                updated_at = NOW()
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssii",
            $nama_lengkap,
            $username,
            $id,
            $id
        );
    }

    if (!mysqli_stmt_execute($stmt)) {
        return [
            'status' => false,
            'message' => mysqli_stmt_error($stmt)
        ];
    }

    return [
        'status' => true,
        'affected_rows' => mysqli_stmt_affected_rows($stmt)
    ];
}

function getUsers($data = [])
{
    global $conn;

    // Pagination
    $page  = isset($data['page']) ? (int)$data['page'] : 1;
    $limit = isset($data['limit']) ? (int)$data['limit'] : 10;
    $search = trim($data['search'] ?? '');

    if ($page < 1) {
        $page = 1;
    }

    $offset = ($page - 1) * $limit;

    // ============================
    // WHERE Search
    // ============================

    $where = "";
    $params = [];
    $types = "";

    if ($search !== '') {

        $where = "WHERE nama_lengkap LIKE ? OR username LIKE ? OR role LIKE ?";

        $keyword = "%{$search}%";

        $params = [
            $keyword,
            $keyword,
            $keyword
        ];

        $types = "sss";
    }

    // ============================
    // Total Data
    // ============================

    $sqlCount = "SELECT COUNT(*) AS total FROM users $where";

    $stmt = mysqli_prepare($conn, $sqlCount);

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);

    $total = mysqli_fetch_assoc(
        mysqli_stmt_get_result($stmt)
    )['total'];

    // ============================
    // Data Users
    // ============================

    $sql = "
        SELECT *
        FROM users
        $where
        ORDER BY id DESC
        LIMIT ?, ?
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if (!empty($params)) {

        $bindTypes = $types . "ii";

        $bindValues = [
            ...$params,
            $offset,
            $limit
        ];

        mysqli_stmt_bind_param(
            $stmt,
            $bindTypes,
            ...$bindValues
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

    return [
        'data' => $rows,
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_pages' => ceil($total / $limit)
    ];
}

function editUsersRoleSetting($data)
{
    global $conn;

    $id   = (int)($data['id'] ?? 0);
    $role = trim($data['role'] ?? '');
    // Validasi ID
    if ($id <= 0) {
        return [
            'status' => false,
            'message' => 'ID user tidak valid.'
        ];
    }

    // Validasi role
    $roles = ['user', 'admin', 'superadmin'];

    if (!in_array($role, $roles)) {
        return [
            'status' => false,
            'message' => 'Role tidak valid.'
        ];
    }

    if ($id == $_SESSION['id'] && $role !== 'superadmin') {
        return [
            'status' => false,
            'message' => 'Anda tidak dapat menurunkan role akun yang sedang digunakan.'
        ];
    }

    // Update role
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE users
         SET
            role = ?,
            updated_by = ?,
            updated_at = NOW()
         WHERE id = ?"
    );

    $updated_by = $_SESSION['id'];

    mysqli_stmt_bind_param(
        $stmt,
        "sii",
        $role,
        $updated_by,
        $id
    );

    if (!mysqli_stmt_execute($stmt)) {
        return [
            'status' => false,
            'message' => mysqli_stmt_error($stmt)
        ];
    }

    return [
        'status' => true,
        'affected_rows' => mysqli_stmt_affected_rows($stmt)
    ];
}
