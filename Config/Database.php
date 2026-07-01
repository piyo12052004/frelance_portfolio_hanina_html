<?php

class Database
{
    private $host = "localhost";
    private $dbname = "data_aset_barang";
    private $username = "root";
    private $password = "";

    protected $conn;

    public function __construct()
    {
        $this->conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->dbname
        );

        if ($this->conn->connect_error) {
            die("Koneksi gagal");
        }
    }
}