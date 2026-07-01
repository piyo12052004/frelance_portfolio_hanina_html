<?php

require_once __DIR__ . '/../Models/BarangMasuk.php';
require_once __DIR__ . '/../Models/Barang.php';
require_once __DIR__ . '/../Models/Supplier.php';

class BarangMasukController
{
    private $model;

    public function __construct()
    {
        $this->model = new BarangMasuk();
    }

    // ======================
    // GET DATA LIST
    // ======================
    public function getData($request)
    {
        $page   = $request['page'] ?? 1;
        $limit  = $request['limit'] ?? 10;
        $search = $request['search'] ?? '';

        return $this->model->getData($page, $limit, $search);
    }

    // ======================
    // DROPDOWN BARANG
    // ======================
    public function getBarang()
    {
        $barang = new Barang();
        return $barang->getAll();
    }

    // ======================
    // DROPDOWN SUPPLIER
    // ======================
    public function getSupplier()
    {
        $supplier = new Supplier();
        return $supplier->getAll();
    }

    // ==========================
    // CREATE DATA
    // ==========================
    public function create($data)
    {
        $result = $this->model->create($data);

        // optional session flash
        if ($result['status']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        return $result;
    }

    public function edit($id)
    {
        return $this->model->getById($id);
    }

    public function update($data)
    {
        return $this->model->update($data);
    }

    public function delete($id)
    {
        return $this->model->delete($id);
    }
}
