<?php

class Database
{
    private $host;
    private $dbname;
    private $username;
    private $password;

    protected $conn;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->dbname = getenv('DB_DATABASE') ?: 'data_aset_barang';
        $this->username = getenv('DB_USERNAME') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';

        $this->conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->dbname
        );

        if ($this->conn->connect_error) {
            die("Koneksi gagal : " . $this->conn->connect_error);
        }
    }
}