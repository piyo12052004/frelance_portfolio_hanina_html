<?php

require_once __DIR__ . '/../Models/Supplier.php';

class SupplierController
{
    private $supplier;

    public function __construct()
    {
        $this->supplier = new Supplier();
    }

    // =========================
    // GET DATA (pagination + search)
    // =========================
    public function getData($page = 1, $limit = 5, $search = "")
    {
        $offset = ($page - 1) * $limit;

        $data = $this->supplier->getData($limit, $offset, $search);
        $total = $this->supplier->countData($search);

        return [
            "data" => $data,
            "total" => $total,
            "page" => $page,
            "limit" => $limit,
            "search" => $search,
            "total_page" => ceil($total / $limit)
        ];
    }

    // =========================
    // CREATE
    // =========================
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $result = $this->supplier->create($_POST);

            if ($result['status']) {
                $_SESSION['success'] = $result['message'];
                header("Location: supplier.php");
                exit;
            }

            return $result;
        }

        return null;
    }

    // =========================
    // GET BY ID
    // =========================
    public function getById($id)
    {
        return $this->supplier->getById($id);
    }

    // =========================
    // UPDATE
    // =========================
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $result = $this->supplier->update($_POST);

            if ($result['status']) {
                $_SESSION['success'] = $result['message'];
                header("Location: supplier.php");
                exit;
            }

            return $result;
        }

        return null;
    }

    // =========================
    // DELETE
    // =========================
    public function delete($id)
    {
        $result = $this->supplier->delete($id);

        if ($result['status']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header("Location: supplier.php");
        exit;
    }
}