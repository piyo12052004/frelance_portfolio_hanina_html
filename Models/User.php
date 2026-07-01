<?php

require_once __DIR__ . '/../Config/Database.php';

class User extends Database
{
    public function register($data)
    {
        $nama = trim($data['nama_lengkap']);
        $username = trim($data['username']);
        $password = $data['password'];
        $konfirmasi = $data['konfirmasi_password'];

        // Validasi
        if (empty($nama) || empty($username) || empty($password) || empty($konfirmasi)) {
            return [
                'status' => false,
                'message' => 'Semua field wajib diisi.'
            ];
        }

        if (strlen($password) < 8) {
            return [
                'status' => false,
                'message' => 'Password minimal 8 karakter.'
            ];
        }

        if ($password != $konfirmasi) {
            return [
                'status' => false,
                'message' => 'Konfirmasi password tidak sama.'
            ];
        }

        // Cek Username
        $cek = $this->conn->prepare("SELECT id FROM users WHERE username=?");
        $cek->bind_param("s", $username);
        $cek->execute();

        if ($cek->get_result()->num_rows > 0) {
            return [
                'status' => false,
                'message' => 'Username sudah digunakan.'
            ];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("
            INSERT INTO users
            (nama_lengkap, username, password)
            VALUES (?, ?, ?)
        ");

        $stmt->bind_param(
            "sss",
            $nama,
            $username,
            $passwordHash
        );

        if ($stmt->execute()) {
            return [
                'status' => true,
                'message' => 'Registrasi berhasil.'
            ];
        }

        return [
            'status' => false,
            'message' => 'Registrasi gagal.'
        ];
    }

    public function login($data)
    {
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        // Validasi
        if (empty($username) || empty($password)) {
            return [
                'status'  => false,
                'message' => 'Username dan Password wajib diisi.'
            ];
        }

        // Query user berdasarkan username
        $stmt = $this->conn->prepare("
        SELECT *
        FROM users
        WHERE username = ?
        LIMIT 1
    ");

        if (!$stmt) {
            return [
                'status'  => false,
                'message' => 'Terjadi kesalahan pada sistem.'
            ];
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        // Username tidak ditemukan
        if ($result->num_rows === 0) {
            $stmt->close();

            return [
                'status'  => false,
                'message' => 'Username tidak ditemukan.'
            ];
        }

        $user = $result->fetch_assoc();

        // Password salah
        if (!password_verify($password, $user['password'])) {
            $stmt->close();

            return [
                'status'  => false,
                'message' => 'Password salah.'
            ];
        }

        $stmt->close();

        return [
            'status'  => true,
            'message' => 'Login berhasil.',
            'user'    => $user
        ];
    }

    public function getUserById($id)
    {
        $stmt = $this->conn->prepare("
        SELECT *
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}
