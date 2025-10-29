<?php
// aset_barang/export.php (versi diperbaiki)
// Pastikan path koneksi sesuai struktur projectmu
include '../../includes/auth_check.php';
include '../../config/db.php';

// include composer autoload jika ada
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

// 'use' berada di file scope (boleh hadir walau class tidak terinstal — tidak menimbulkan parse error)
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil parameter pencarian dari GET
$search = $_GET['search'] ?? '';
$where = '';
if (!empty($search)) {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $where = "WHERE ab.nama_barang LIKE '%{$search_esc}%'
              OR ab.deskripsi LIKE '%{$search_esc}%'
              OR k.nama_kategori LIKE '%{$search_esc}%'
              OR l.nama_lokasi LIKE '%{$search_esc}%'
              OR p.nama_program LIKE '%{$search_esc}%'
              OR ab.kondisi_barang LIKE '%{$search_esc}%'
              OR ab.penanggung_jawab LIKE '%{$search_esc}%'";
}

// Query data (ambil semua)
$sql = "
  SELECT ab.*,
        k.nama_kategori,
        l.nama_lokasi,
        p.nama_program
  FROM aset_barang ab
  LEFT JOIN kategori_barang k ON ab.kategori_barang = k.id
  LEFT JOIN lokasi_barang l ON ab.lokasi_barang = l.id
  LEFT JOIN program_pendanaan p ON ab.program_pendanaan = p.id
  {$where}
  ORDER BY ab.id DESC
";

$res = mysqli_query($conn, $sql);
if (!$res) {
    // keluarkan error DB supaya bisa debug
    die('Query error: ' . mysqli_error($conn));
}

// Ambil semua baris ke array agar aman dipakai berulang kali
$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    $rows[] = $r;
}

// Jika PhpSpreadsheet tersedia => buat .xlsx, jika tidak => fallback .xls (HTML)
if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
    // Buat spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
    $headers = [
      'No','Nama Barang','Deskripsi','Kategori','Kondisi','Lokasi',
      'Program Pendanaan','Jumlah Unit','Nomor Seri','Harga Pembelian',
      'Tanggal Perolehan','Status Penggunaan','Penanggung Jawab','Tanggal Pajak'
    ];
    $col = 'A';
    foreach ($headers as $h) {
        $sheet->setCellValue($col . '1', $h);
        $sheet->getStyle($col . '1')->getFont()->setBold(true);
        $col++;
    }

    $rowNum = 2;
    $no = 1;
    foreach ($rows as $row) {
        $sheet->setCellValue("A{$rowNum}", $no);
        $sheet->setCellValue("B{$rowNum}", $row['nama_barang']);
        $sheet->setCellValue("C{$rowNum}", $row['deskripsi']);
        $sheet->setCellValue("D{$rowNum}", $row['nama_kategori'] ?? '-');
        $sheet->setCellValue("E{$rowNum}", $row['kondisi_barang']);
        $sheet->setCellValue("F{$rowNum}", $row['nama_lokasi'] ?? '-');
        $sheet->setCellValue("G{$rowNum}", $row['nama_program'] ?? '-');
        $sheet->setCellValue("H{$rowNum}", $row['jumlah_unit']);
        $sheet->setCellValue("I{$rowNum}", $row['nomor_seri']);
        // tulis angka mentah supaya bisa diformat
        $sheet->setCellValue("J{$rowNum}", is_numeric($row['harga_pembelian']) ? (float)$row['harga_pembelian'] : 0);
        // format rupiah (tanpa desimal)
        $sheet->getStyle("J{$rowNum}")->getNumberFormat()->setFormatCode('"Rp" #,##0');
        $sheet->setCellValue("K{$rowNum}", $row['waktu_perolehan']);
        $sheet->setCellValue("L{$rowNum}", $row['status_penggunaan']);
        $sheet->setCellValue("M{$rowNum}", $row['penanggung_jawab']);
        $sheet->setCellValue("N{$rowNum}", $row['tanggal_pajak'] ?: '-');

        $rowNum++;
        $no++;
    }

    // auto size semua kolom yang dipakai (A..N)
    foreach (range('A','N') as $c) {
        $sheet->getColumnDimension($c)->setAutoSize(true);
    }

    // Kirim file XLSX
    $filename = 'Data_Aset_' . date('Y-m-d_H-i-s') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} else {
    // Fallback: output HTML table dengan header Content-Type Excel (.xls)
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    $filename = 'Data_Aset_' . date('Y-m-d_H-i-s') . '.xls';
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />";
    echo "<h3>Data Aset PRCF Indonesia</h3>";
    echo "<p>Filter pencarian: <b>" . htmlspecialchars($search, ENT_QUOTES) . "</b></p>";
    echo "<table border='1' style='border-collapse:collapse;'>";
    echo "<thead><tr>
            <th>No</th><th>Nama Barang</th><th>Deskripsi</th><th>Kategori</th><th>Kondisi</th>
            <th>Lokasi</th><th>Program Pendanaan</th><th>Jumlah Unit</th><th>Nomor Seri</th>
            <th>Harga Pembelian</th><th>Tanggal Perolehan</th><th>Status Penggunaan</th><th>Penanggung Jawab</th><th>Tanggal Pajak</th>
          </tr></thead><tbody>";

    $no = 1;
    foreach ($rows as $row) {
        $harga_formatted = 'Rp ' . number_format($row['harga_pembelian'] ?? 0, 0, ',', '.');
        echo '<tr>';
        echo '<td>' . $no . '</td>';
        echo '<td>' . htmlspecialchars($row['nama_barang'] ?? '', ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['deskripsi'] ?? '', ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['nama_kategori'] ?? '-', ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['kondisi_barang'] ?? '', ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['nama_lokasi'] ?? '-', ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['nama_program'] ?? '-', ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['jumlah_unit'] ?? '', ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['nomor_seri'] ?? '', ENT_QUOTES) . '</td>';
        echo '<td>' . $harga_formatted . '</td>';
        echo '<td>' . htmlspecialchars($row['waktu_perolehan'] ?? '', ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['status_penggunaan'] ?? '', ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['penanggung_jawab'] ?? '', ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['tanggal_pajak'] ?: '-', ENT_QUOTES) . '</td>';
        echo '</tr>';
        $no++;
    }

    echo "</tbody></table>";
    exit;
}
