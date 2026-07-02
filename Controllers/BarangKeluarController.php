<?php

require_once __DIR__ . '/../Models/BarangKeluar.php';
require_once __DIR__ . '/../Models/Barang.php';

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// use FPDF;

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

    public function exportExcel()
    {
        $data = $this->model->exportData();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nomor Transaksi');
        $sheet->setCellValue('C1', 'Kode Barang');
        $sheet->setCellValue('D1', 'Nama Barang');
        $sheet->setCellValue('E1', 'Tanggal');
        $sheet->setCellValue('F1', 'Jumlah');
        $sheet->setCellValue('G1', 'Tujuan');
        $sheet->setCellValue('H1', 'Keterangan');

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $row = 2;
        $no = 1;

        foreach ($data as $item) {

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['nomor_transaksi']);
            $sheet->setCellValue('C' . $row, $item['kode_barang']);
            $sheet->setCellValue('D' . $row, $item['nama_barang']);
            $sheet->setCellValue('E' . $row, $item['tanggal']);
            $sheet->setCellValue('F' . $row, $item['jumlah']);
            $sheet->setCellValue('G' . $row, $item['tujuan']);
            $sheet->setCellValue('H' . $row, $item['keterangan']);

            $row++;
        }

        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="BarangKeluar.xlsx"');
        header('Cache-Control: max-age=0');

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
        $pdf->Cell(0, 10, 'Laporan Barang Keluar', 0, 1, 'C');

        $pdf->Ln(3);

        $pdf->SetFont('Arial', 'B', 10);

        $pdf->Cell(10, 8, 'No', 1);
        $pdf->Cell(45, 8, 'No Transaksi', 1);
        $pdf->Cell(30, 8, 'Kode', 1);
        $pdf->Cell(60, 8, 'Nama Barang', 1);
        $pdf->Cell(28, 8, 'Tanggal', 1);
        $pdf->Cell(20, 8, 'Jumlah', 1);
        $pdf->Cell(45, 8, 'Tujuan', 1);
        $pdf->Cell(50, 8, 'Keterangan', 1);

        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);

        $no = 1;

        foreach ($data as $row) {

            $pdf->Cell(10, 8, $no++, 1);
            $pdf->Cell(45, 8, $row['nomor_transaksi'], 1);
            $pdf->Cell(30, 8, $row['kode_barang'], 1);
            $pdf->Cell(60, 8, substr($row['nama_barang'], 0, 30), 1);
            $pdf->Cell(28, 8, $row['tanggal'], 1);
            $pdf->Cell(20, 8, $row['jumlah'], 1);
            $pdf->Cell(45, 8, substr($row['tujuan'], 0, 20), 1);
            $pdf->Cell(50, 8, substr($row['keterangan'], 0, 25), 1);

            $pdf->Ln();
        }

        $pdf->Output('I', 'BarangKeluar.pdf');
        exit;
    }
}
