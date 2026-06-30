<?php
require_once __DIR__ . '/../Config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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