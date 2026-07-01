<?php

require_once __DIR__ . '/../Config/Database.php';

class Barang extends Database
{
    public function getData($limit, $offset, $search = "")
    {
        $search = $this->conn->real_escape_string($search);

        $where = "";

        if (!empty($search)) {
            $where = "WHERE 
                b.nama_barang LIKE '%$search%' 
                OR b.kode_barang LIKE '%$search%'";
        }

        // data
        $query = "
            SELECT 
                b.*,
                k.nama_kategori,
                l.nama_lokasi
            FROM barang_t b
            LEFT JOIN kategori_m k ON b.kategori_id = k.id
            LEFT JOIN lokasi_m l ON b.lokasi_id = l.id
            $where
            ORDER BY b.id DESC
            LIMIT $limit OFFSET $offset
        ";

        $result = $this->conn->query($query);

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // total data (pagination)
        $countQuery = "
            SELECT COUNT(*) as total 
            FROM barang_t b
            $where
        ";

        $countResult = $this->conn->query($countQuery);
        $total = $countResult->fetch_assoc()['total'];

        return [
            "data" => $data,
            "total" => $total
        ];
    }

    public function getKodeBarang()
    {
        $query = "SELECT kode_barang 
              FROM barang_t 
              WHERE kode_barang LIKE 'AST-%'
              ORDER BY id DESC 
              LIMIT 1";

        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();

        if (!$row) {
            return "AST-001";
        }

        $lastNumber = (int) substr($row['kode_barang'], 4);
        $nextNumber = $lastNumber + 1;

        return "AST-" . str_pad($nextNumber, 3, "0", STR_PAD_LEFT);
    }

    public function getDataKategoriDanLokasi()
    {
        // KATEGORI
        $kategoriQuery = "SELECT * FROM kategori_m ORDER BY id DESC";
        $kategoriResult = $this->conn->query($kategoriQuery);

        $kategori = [];
        while ($row = $kategoriResult->fetch_assoc()) {
            $kategori[] = $row;
        }

        // LOKASI
        $lokasiQuery = "SELECT * FROM lokasi_m ORDER BY id DESC";
        $lokasiResult = $this->conn->query($lokasiQuery);

        $lokasi = [];
        while ($row = $lokasiResult->fetch_assoc()) {
            $lokasi[] = $row;
        }

        return [
            "kategori" => $kategori,
            "lokasi" => $lokasi
        ];
    }

    public function create($data, $files)
    {
        session_start();

        $kode_barang = $this->conn->real_escape_string($data['kode_barang'] ?? '');
        $nama_barang = $this->conn->real_escape_string($data['nama_barang'] ?? '');
        $kategori_id = (int) ($data['kategori_id'] ?? 0);
        $lokasi_id   = (int) ($data['lokasi_id'] ?? 0);
        $merk        = $this->conn->real_escape_string($data['merk'] ?? '');
        $tipe        = $this->conn->real_escape_string($data['tipe'] ?? '');
        $kondisi     = $this->conn->real_escape_string($data['kondisi'] ?? '');
        $stok        = (int) ($data['stok'] ?? 0);
        $created_by  = $_SESSION['id'] ?? 0;

        // ======================
        // VALIDASI DASAR
        // ======================
        if ($created_by === 0) {
            return ['status' => false, 'message' => 'Session login tidak ditemukan.'];
        }

        if (
            empty($kode_barang) ||
            empty($nama_barang) ||
            $kategori_id == 0 ||
            $lokasi_id == 0 ||
            empty($kondisi)
        ) {
            return ['status' => false, 'message' => 'Semua data wajib harus diisi.'];
        }

        if ($stok < 0) {
            return ['status' => false, 'message' => 'Stok tidak boleh kurang dari 0.'];
        }

        // ======================
        // CEK KODE DUPLIKAT
        // ======================
        $cek = $this->conn->query("SELECT id FROM barang_t WHERE kode_barang = '$kode_barang'");
        if ($cek->num_rows > 0) {
            return ['status' => false, 'message' => 'Kode barang sudah digunakan.'];
        }

        // ======================
        // VALIDASI TAHUN
        // ======================
        $tahun = trim($data['tahun'] ?? '');

        if ($tahun === '') {
            $tahunSql = "NULL";
        } elseif (!is_numeric($tahun) || $tahun < 1901 || $tahun > 2155) {
            return ['status' => false, 'message' => 'Tahun tidak valid.'];
        } else {
            $tahunSql = (int) $tahun;
        }

        // ======================
        // UPLOAD FOTO
        // ======================
        $fotoName = null;

        if (!empty($files['foto']['name'])) {

            $allowedExt = ['jpg', 'jpeg', 'png'];

            $fileName = $files['foto']['name'];
            $fileTmp  = $files['foto']['tmp_name'];
            $fileSize = $files['foto']['size'];

            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExt)) {
                return ['status' => false, 'message' => 'Format foto harus JPG, JPEG, PNG'];
            }

            if ($fileSize > 2 * 1024 * 1024) {
                return ['status' => false, 'message' => 'Ukuran foto maksimal 2MB'];
            }

            $fotoName = 'barang_' . time() . '_' . rand(1000, 9999) . '.' . $ext;

            $uploadDir = __DIR__ . '/../Uploads/barang/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            move_uploaded_file($fileTmp, $uploadDir . $fotoName);
        }

        // ======================
        // INSERT DATA
        // ======================
        $query = "
            INSERT INTO barang_t (
                kode_barang,
                nama_barang,
                kategori_id,
                lokasi_id,
                merk,
                tipe,
                tahun,
                kondisi,
                stok,
                foto,
                created_by,
                created_at
            ) VALUES (
                '$kode_barang',
                '$nama_barang',
                $kategori_id,
                $lokasi_id,
                '$merk',
                '$tipe',
                $tahunSql,
                '$kondisi',
                $stok,
                '$fotoName',
                $created_by,
                NOW()
            )
        ";

        if ($this->conn->query($query)) {
            return ['status' => true, 'message' => 'Data barang berhasil ditambahkan.'];
        }

        return ['status' => false, 'message' => $this->conn->error];
    }

    public function getById($id)
    {
        $id = (int)$id;

        $query = "
            SELECT *
            FROM barang_t
            WHERE id = $id
            LIMIT 1
        ";

        $result = $this->conn->query($query);

        return $result->fetch_assoc();
    }

    public function update($data, $files, $id)
    {
        session_start();

        $id = (int)$id;

        $kode_barang = $this->conn->real_escape_string($data['kode_barang'] ?? '');
        $nama_barang = $this->conn->real_escape_string($data['nama_barang'] ?? '');
        $kategori_id = (int)($data['kategori_id'] ?? 0);
        $lokasi_id   = (int)($data['lokasi_id'] ?? 0);
        $merk        = $this->conn->real_escape_string($data['merk'] ?? '');
        $tipe        = $this->conn->real_escape_string($data['tipe'] ?? '');
        $kondisi     = $this->conn->real_escape_string($data['kondisi'] ?? '');
        $stok        = (int)($data['stok'] ?? 0);
        $updated_by  = $_SESSION['id'] ?? 0;

        if ($updated_by === 0) {
            return ['status' => false, 'message' => 'Session tidak ditemukan'];
        }

        if (empty($nama_barang) || $kategori_id == 0 || $lokasi_id == 0) {
            return ['status' => false, 'message' => 'Data wajib belum lengkap'];
        }

        // =========================
        // GET FOTO LAMA
        // =========================
        $old = $this->getById($id);
        $fotoName = $old['foto'];

        // =========================
        // UPLOAD FOTO BARU (optional)
        // =========================
        if (!empty($files['foto']['name'])) {

            $allowed = ['jpg', 'jpeg', 'png'];

            $fileName = $files['foto']['name'];
            $fileTmp  = $files['foto']['tmp_name'];
            $fileSize = $files['foto']['size'];

            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                return ['status' => false, 'message' => 'Format foto tidak valid'];
            }

            if ($fileSize > 2 * 1024 * 1024) {
                return ['status' => false, 'message' => 'Maksimal 2MB'];
            }

            $fotoName = 'barang_' . time() . rand(1000, 9999) . '.' . $ext;

            $path = __DIR__ . '/../Uploads/barang/';

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            move_uploaded_file($fileTmp, $path . $fotoName);

            // hapus foto lama
            if (!empty($old['foto']) && file_exists($path . $old['foto'])) {
                unlink($path . $old['foto']);
            }
        }

        // =========================
        // VALIDASI TAHUN
        // =========================
        $tahun = trim($data['tahun'] ?? '');

        if ($tahun === '') {
            $tahunSql = "NULL";
        } else {
            $tahunSql = (int)$tahun;
        }

        // =========================
        // QUERY UPDATE
        // =========================
        $query = "
            UPDATE barang_t SET
                kode_barang = '$kode_barang',
                nama_barang = '$nama_barang',
                kategori_id = $kategori_id,
                lokasi_id   = $lokasi_id,
                merk        = '$merk',
                tipe        = '$tipe',
                tahun       = $tahunSql,
                kondisi     = '$kondisi',
                stok        = $stok,
                foto        = '$fotoName',
                updated_by  = $updated_by,
                updated_at  = NOW()
            WHERE id = $id
        ";

        if ($this->conn->query($query)) {
            return ['status' => true, 'message' => 'Data berhasil diupdate'];
        }

        return ['status' => false, 'message' => $this->conn->error];
    }

    public function delete($id)
    {
        session_start();

        $id = (int)$id;

        // ambil data dulu (untuk hapus foto)
        $query = "SELECT foto FROM barang_t WHERE id = $id LIMIT 1";
        $result = $this->conn->query($query);

        if ($result->num_rows == 0) {
            return [
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ];
        }

        $data = $result->fetch_assoc();
        $foto = $data['foto'];

        // hapus data di database
        $deleteQuery = "DELETE FROM barang_t WHERE id = $id";

        if ($this->conn->query($deleteQuery)) {

            // hapus file foto kalau ada
            $path = __DIR__ . '/../Uploads/barang/' . $foto;

            if (!empty($foto) && file_exists($path)) {
                unlink($path);
            }

            return [
                'status' => true,
                'message' => 'Data barang berhasil dihapus.'
            ];
        }

        return [
            'status' => false,
            'message' => $this->conn->error
        ];
    }
    
    public function getAll()
    {
        $result = $this->conn->query("SELECT id, nama_barang,stok FROM barang_t ORDER BY nama_barang ASC");

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}
