<?php

require_once __DIR__ . '/../Models/BarangMasuk.php';
require_once __DIR__ . '/../Models/Barang.php';
require_once __DIR__ . '/../Models/Supplier.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// use FPDF;

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

    public function exportExcel()
    {
        $data = $this->model->exportData();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Supplier');
        $sheet->setCellValue('E1', 'Tanggal');
        $sheet->setCellValue('F1', 'Jumlah');
        $sheet->setCellValue('G1', 'Harga');
        $sheet->setCellValue('H1', 'Keterangan');

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $row = 2;
        $no = 1;

        foreach ($data as $item) {

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['kode_barang']);
            $sheet->setCellValue('C' . $row, $item['nama_barang']);
            $sheet->setCellValue('D' . $row, $item['nama_supplier']);
            $sheet->setCellValue('E' . $row, $item['tanggal']);
            $sheet->setCellValue('F' . $row, $item['jumlah']);
            $sheet->setCellValue('G' . $row, $item['harga']);
            $sheet->setCellValue('H' . $row, $item['keterangan']);

            $row++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="BarangMasuk.xlsx"');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPDF()
    {
        $data = $this->model->exportData();

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Laporan Barang Masuk', 0, 1, 'C');

        $pdf->Ln(3);

        $pdf->SetFont('Arial', 'B', 10);

        $pdf->Cell(10, 8, 'No', 1);
        $pdf->Cell(30, 8, 'Kode', 1);
        $pdf->Cell(55, 8, 'Nama Barang', 1);
        $pdf->Cell(45, 8, 'Supplier', 1);
        $pdf->Cell(28, 8, 'Tanggal', 1);
        $pdf->Cell(20, 8, 'Jumlah', 1);
        $pdf->Cell(35, 8, 'Harga', 1);
        $pdf->Cell(55, 8, 'Keterangan', 1);

        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);

        $no = 1;

        foreach ($data as $row) {

            $pdf->Cell(10, 8, $no++, 1);
            $pdf->Cell(30, 8, $row['kode_barang'], 1);
            $pdf->Cell(55, 8, substr($row['nama_barang'], 0, 28), 1);
            $pdf->Cell(45, 8, substr($row['nama_supplier'], 0, 20), 1);
            $pdf->Cell(28, 8, $row['tanggal'], 1);
            $pdf->Cell(20, 8, $row['jumlah'], 1);
            $pdf->Cell(35, 8, number_format($row['harga'], 0, ',', '.'), 1);
            $pdf->Cell(55, 8, substr($row['keterangan'], 0, 25), 1);

            $pdf->Ln();
        }

        $pdf->Output('I', 'BarangMasuk.pdf');
        exit;
    }
}
