<?php
// modules/aset_barang/export.php
include '../../includes/auth_check.php';
include '../../config/db.php';

// include composer autoload jika tersedia
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// --- Ambil parameter GET (filter pencarian dan kategori)
$search   = $_GET['search']   ?? '';
$kategori = isset($_GET['kategori']) ? (int) $_GET['kategori'] : 0;

// --- Bangun klausa WHERE dinamis
$where = [];
if (!empty($search)) {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $where[] = "(ab.nama_barang LIKE '%{$search_esc}%'
              OR ab.deskripsi LIKE '%{$search_esc}%'
              OR k.nama_kategori LIKE '%{$search_esc}%'
              OR l.nama_lokasi LIKE '%{$search_esc}%'
              OR p.nama_program LIKE '%{$search_esc}%'
              OR ab.kondisi_barang LIKE '%{$search_esc}%'
              OR ab.penanggung_jawab LIKE '%{$search_esc}%')";
}

if ($kategori > 0) {
    $where[] = "ab.kategori_barang = {$kategori}";
}

// Gabungkan jadi satu string WHERE (jika ada)
$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

// --- Query data
$sql = "
  SELECT ab.*,
        k.nama_kategori,
        l.nama_lokasi,
        p.nama_program
  FROM aset_barang ab
  LEFT JOIN kategori_barang k ON ab.kategori_barang = k.id
  LEFT JOIN lokasi_barang l ON ab.lokasi_barang = l.id
  LEFT JOIN program_pendanaan p ON ab.program_pendanaan = p.id
  {$whereSql}
  ORDER BY ab.id DESC
";

$res = mysqli_query($conn, $sql);
if (!$res) {
    die('Query error: ' . mysqli_error($conn));
}

// --- Ambil hasil
$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    $rows[] = $r;
}

// --- Cek jika data kosong
if (empty($rows)) {
    die("<script>alert('Tidak ada data untuk diexport.'); window.history.back();</script>");
}

// --- Export menggunakan PhpSpreadsheet jika tersedia
if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
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

    // Isi data
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
        $sheet->setCellValue("J{$rowNum}", (float) ($row['harga_pembelian'] ?? 0));
        $sheet->getStyle("J{$rowNum}")->getNumberFormat()->setFormatCode('"Rp" #,##0');
        $sheet->setCellValue("K{$rowNum}", $row['waktu_perolehan']);
        $sheet->setCellValue("L{$rowNum}", $row['status_penggunaan']);
        $sheet->setCellValue("M{$rowNum}", $row['penanggung_jawab']);
        $sheet->setCellValue("N{$rowNum}", $row['tanggal_pajak'] ?: '-');

        $rowNum++; $no++;
    }

    foreach (range('A', 'N') as $c) {
        $sheet->getColumnDimension($c)->setAutoSize(true);
    }

    // Output ke browser
    $filename = 'Data_Aset_' . date('Y-m-d_H-i-s') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// --- Fallback HTML (jika PhpSpreadsheet tidak ada)
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
$filename = 'Data_Aset_' . date('Y-m-d_H-i-s') . '.xls';
header("Content-Disposition: attachment; filename=\"{$filename}\"");
echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />";
echo "<h3>Data Aset PRCF Indonesia</h3>";
echo "<p>Filter pencarian: <b>" . htmlspecialchars($search, ENT_QUOTES) . "</b></p>";
echo "<table border='1' style='border-collapse:collapse;'>";
echo "<thead><tr>";
foreach ($headers as $h) echo "<th>{$h}</th>";
echo "</tr></thead><tbody>";

$no = 1;
foreach ($rows as $row) {
    echo "<tr>";
    echo "<td>{$no}</td>";
    echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
    echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_kategori'] ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($row['kondisi_barang']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_lokasi'] ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_program'] ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($row['jumlah_unit']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nomor_seri']) . "</td>";
    echo "<td>Rp " . number_format($row['harga_pembelian'] ?? 0, 0, ',', '.') . "</td>";
    echo "<td>" . htmlspecialchars($row['waktu_perolehan']) . "</td>";
    echo "<td>" . htmlspecialchars($row['status_penggunaan']) . "</td>";
    echo "<td>" . htmlspecialchars($row['penanggung_jawab']) . "</td>";
    echo "<td>" . htmlspecialchars($row['tanggal_pajak'] ?: '-') . "</td>";
    echo "</tr>";
    $no++;
}
echo "</tbody></table>";
exit;
?>
