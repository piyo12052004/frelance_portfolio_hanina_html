<?php

require_once '../../vendor/autoload.php';
require_once '../../Controllers/BarangMasukController.php';

$controller = new BarangMasukController();
$controller->exportPDF();