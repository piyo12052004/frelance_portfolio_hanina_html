<?php

require_once __DIR__ . '/../Models/Kategori.php';

class KategoriController
{
    private $kategori;

    public function __construct()
    {
        $this->kategori = new Kategori();
    }

    public function getData($page = 1, $limit = 5, $search = "")
    {
        $offset = ($page - 1) * $limit;

        $data = $this->kategori->getData($limit, $offset, $search);
        $total = $this->kategori->countData($search);

        return [
            "data" => $data,
            "total" => $total,
            "page" => $page,
            "limit" => $limit,
            "search" => $search,
            "total_page" => ceil($total / $limit)
        ];
    }


    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $result = $this->kategori->update($_POST);

            if ($result['status']) {

                $_SESSION['success'] = $result['message'];

                header("Location: katagori.php");
                exit;
            }

            return $result;
        }

        return null;
    }

    public function getById($id)
    {
        return $this->kategori->getById($id);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $result = $this->kategori->create($_POST);

            if ($result['status']) {

                $_SESSION['success'] = $result['message'];

                header("Location: katagori.php");

                exit;
            }

            return $result;
        }

        return null;
    }
    public function delete($id)
    {
        $result = $this->kategori->delete($id);

        if ($result['status']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header("Location: katagori.php");
        exit;
    }
}
