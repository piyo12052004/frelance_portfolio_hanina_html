<?php

require_once '../../vendor/autoload.php';
require_once '../../Controllers/BarangKeluarController.php';

$controller = new BarangKeluarController();
$controller->exportExcel();