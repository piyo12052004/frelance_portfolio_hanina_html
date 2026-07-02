<?php

session_start();

require_once '../../Controllers/BarangController.php';

$controller = new BarangController();
$controller->exportExcel();