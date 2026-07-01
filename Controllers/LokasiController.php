<?php

require_once __DIR__ . '/../Models/Lokasi.php';

class LokasiController
{
    private $lokasi;

    public function __construct()
    {
        $this->lokasi = new Lokasi();
    }

    public function getData($page = 1, $limit = 5, $search = "")
    {
        $offset = ($page - 1) * $limit;

        $data = $this->lokasi->getData($limit, $offset, $search);
        $total = $this->lokasi->countData($search);

        return [
            "data" => $data,
            "total" => $total,
            "page" => $page,
            "limit" => $limit,
            "search" => $search,
            "total_page" => ceil($total / $limit)
        ];
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $result = $this->lokasi->create($_POST);

            if ($result['status']) {
                $_SESSION['success'] = $result['message'];
                header("Location: lokasi.php");
                exit;
            }

            return $result;
        }

        return null;
    }

    public function getById($id)
    {
        return $this->lokasi->getById($id);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $result = $this->lokasi->update($_POST);

            if ($result['status']) {
                $_SESSION['success'] = $result['message'];
                header("Location: lokasi.php");
                exit;
            }

            return $result;
        }

        return null;
    }

    public function delete($id)
    {
        $result = $this->lokasi->delete($id);

        if ($result['status']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header("Location: lokasi.php");
        exit;
    }
}
