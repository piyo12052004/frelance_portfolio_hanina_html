<?php

require_once __DIR__ . '/../Models/Barang.php';

class BarangController
{
    private $barang;

    public function __construct()
    {
        $this->barang = new Barang();
    }

    public function getData($limit = 10, $page = 1, $search = "")
    {
        $offset = ($page - 1) * $limit;

        $result = $this->barang->getData($limit, $offset, $search);

        $totalData = $result['total'];
        $totalPage = ceil($totalData / $limit);

        return [
            "data" => $result['data'],
            "pagination" => [
                "page" => $page,
                "limit" => $limit,
                "total_data" => $totalData,
                "total_page" => $totalPage,
                "offset" => $offset,
                "has_prev" => $page > 1,
                "has_next" => $page < $totalPage
            ]
        ];
    }

    public function getKodeBarang()
    {
        return $this->barang->getKodeBarang();
    }

    public function getDataKategoriDanLokasi()
    {
        return $this->barang->getDataKategoriDanLokasi();
    }

    public function create($data, $files)
    {
        $result = $this->barang->create($data, $files);

        if ($result['status']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header("Location: barang.php");
        exit;
    }

    public function getById($id)
    {
        return $this->barang->getById($id);
    }

    public function update($data, $files, $id)
    {
        $result = $this->barang->update($data, $files, $id);

        if ($result['status']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header("Location: barang.php");
        exit;
    }

    public function delete($id)
    {
        $result = $this->barang->delete($id);

        if ($result['status']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header("Location: barang.php");
        exit;
    }
}
