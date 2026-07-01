<?php

require_once __DIR__ . '/../Models/User.php';

class AuthController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $result = $this->user->register($_POST);

            // Jika berhasil
            if ($result['status']) {

                // Opsional: simpan pesan sukses
                session_start();
                $_SESSION['success'] = $result['message'];

                header("Location: login.php");
                exit;
            }

            // Jika gagal, kirim kembali ke View
            return $result;
        }

        return null;
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $result = $this->user->login($_POST);

            if ($result['status']) {

                $_SESSION['login'] = true;
                $_SESSION['id'] = $result['user']['id'];
                $_SESSION['nama_lengkap'] = $result['user']['nama_lengkap'];
                $_SESSION['username'] = $result['user']['username'];
                $_SESSION['role'] = $result['user']['role'];

                // Remember Me
                if (isset($_POST['remember'])) {

                    setcookie(
                        "remember_login",
                        $result['user']['id'],
                        time() + (60 * 60 * 24 * 30),
                        "/"
                    );
                }

                header("Location: ../../index.php");
                exit;
            }

            return $result;
        }

        return null;
    }
}
