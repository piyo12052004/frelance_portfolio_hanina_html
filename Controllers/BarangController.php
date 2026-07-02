<?php

require_once __DIR__ . '/../Models/Barang.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// use FPDF;

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

    public function exportPdf()
    {
        $data = $this->barang->exportData();

        $pdf = new FPDF('L', 'mm', 'A4');

        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'LAPORAN DATA BARANG', 0, 1, 'C');

        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 10);

        $pdf->Cell(10, 8, 'No', 1, 0, 'C');
        $pdf->Cell(28, 8, 'Kode', 1, 0, 'C');
        $pdf->Cell(48, 8, 'Nama Barang', 1, 0, 'C');
        $pdf->Cell(35, 8, 'Kategori', 1, 0, 'C');
        $pdf->Cell(35, 8, 'Lokasi', 1, 0, 'C');
        $pdf->Cell(30, 8, 'Merk', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Tipe', 1, 0, 'C');
        $pdf->Cell(18, 8, 'Tahun', 1, 0, 'C');
        $pdf->Cell(30, 8, 'Kondisi', 1, 0, 'C');
        $pdf->Cell(15, 8, 'Stok', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 9);

        $no = 1;

        foreach ($data as $row) {

            $pdf->Cell(10, 8, $no++, 1, 0, 'C');
            $pdf->Cell(28, 8, $row['kode_barang'], 1);
            $pdf->Cell(48, 8, $row['nama_barang'], 1);
            $pdf->Cell(35, 8, $row['nama_kategori'], 1);
            $pdf->Cell(35, 8, $row['nama_lokasi'], 1);
            $pdf->Cell(30, 8, $row['merk'], 1);
            $pdf->Cell(25, 8, $row['tipe'], 1);
            $pdf->Cell(18, 8, $row['tahun'], 1, 0, 'C');
            $pdf->Cell(30, 8, $row['kondisi'], 1);
            $pdf->Cell(15, 8, $row['stok'], 1, 1, 'C');
        }

        $pdf->Output('D', 'DataBarang.pdf');
        exit;
    }

    public function exportExcel()
    {
        $barang = $this->barang->exportData();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul Kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Kategori');
        $sheet->setCellValue('E1', 'Lokasi');
        $sheet->setCellValue('F1', 'Merk');
        $sheet->setCellValue('G1', 'Tipe');
        $sheet->setCellValue('H1', 'Tahun');
        $sheet->setCellValue('I1', 'Kondisi');
        $sheet->setCellValue('J1', 'Stok');

        // Header Bold
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $row = 2;
        $no = 1;

        foreach ($barang as $item) {

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['kode_barang']);
            $sheet->setCellValue('C' . $row, $item['nama_barang']);
            $sheet->setCellValue('D' . $row, $item['nama_kategori']);
            $sheet->setCellValue('E' . $row, $item['nama_lokasi']);
            $sheet->setCellValue('F' . $row, $item['merk']);
            $sheet->setCellValue('G' . $row, $item['tipe']);
            $sheet->setCellValue('H' . $row, $item['tahun']);
            $sheet->setCellValue('I' . $row, $item['kondisi']);
            $sheet->setCellValue('J' . $row, $item['stok']);

            $row++;
        }

        // Auto Width
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="DataBarang.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
