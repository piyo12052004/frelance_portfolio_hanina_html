<?php

require_once __DIR__ . '/../Models/BarangKeluar.php';
require_once __DIR__ . '/../Models/Barang.php';

class BarangKeluarController
{
    private $model;

    public function __construct()
    {
        $this->model = new BarangKeluar();
    }

    public function getData($request)
    {
        $page   = $request['page'] ?? 1;
        $limit  = $request['limit'] ?? 10;
        $search = $request['search'] ?? '';

        return $this->model->getData($page, $limit, $search);
    }


    public function create($data)
    {
        return $this->model->create($data);
    }

    public function getNomorTransaksi()
    {
        return $this->model->getNomorTransaksi();
    }

    public function getBarang()
    {
        $barang = new Barang();
        return $barang->getAll();
    }

    public function findById($id)
    {
        return $this->model->findById($id);
    }

    public function update($data)
    {
        return $this->model->update($data);
    }

    public function delete($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $result = $this->model->delete($id);

        if ($result['status']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header("Location: barang-keluar.php");
        exit;
    }
}
