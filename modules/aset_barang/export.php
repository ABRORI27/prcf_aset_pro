<?php
// modules/aset_barang/export.php
include '../../includes/auth_check.php';
include '../../config/db.php';

ob_clean();
ob_start();

// Include TCPDF
require_once('../../assets/tcpdf/tcpdf.php');

// --- Ambil SEMUA parameter filter dari URL
$search = $_GET['search'] ?? '';
$kategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$tahun_filter = $_GET['tahun'] ?? '';
$bulan_filter = $_GET['bulan'] ?? '';
$filter_nama = $_GET['nama'] ?? '';
$filter_kondisi = $_GET['kondisi'] ?? '';
$filter_lokasi = $_GET['lokasi'] ?? '';
$filter_program = $_GET['program'] ?? '';

// --- Bangun klausa WHERE dinamis dengan SEMUA filter
$where = [];
$params = [];
$types = '';

// Filter pencarian global
if (!empty($search)) {
    $where[] = "(ab.nama_barang LIKE ? OR ab.deskripsi LIKE ? OR k.nama_kategori LIKE ? OR l.nama_lokasi LIKE ? OR p.nama_program LIKE ? OR ab.kondisi_barang LIKE ? OR ab.penanggung_jawab LIKE ?)";
    $search_term = "%$search%";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term, $search_term, $search_term, $search_term]);
    $types .= str_repeat('s', 7);
}

// Filter kategori
if ($kategori > 0) {
    $where[] = "ab.kategori_barang = ?";
    $params[] = $kategori;
    $types .= 'i';
}

// Filter periode
if ($tahun_filter) {
    $where[] = "ab.periode_tahun = ?";
    $params[] = $tahun_filter;
    $types .= 'i';
}

if ($bulan_filter) {
    $where[] = "ab.periode_bulan = ?";
    $params[] = $bulan_filter;
    $types .= 'i';
}

// Filter dari dropdown
if (!empty($filter_nama)) {
    $where[] = "ab.nama_barang LIKE ?";
    $params[] = "%$filter_nama%";
    $types .= 's';
}

if (!empty($filter_kondisi)) {
    $where[] = "ab.kondisi_barang = ?";
    $params[] = $filter_kondisi;
    $types .= 's';
}

if (!empty($filter_lokasi)) {
    $where[] = "l.nama_lokasi LIKE ?";
    $params[] = "%$filter_lokasi%";
    $types .= 's';
}

if (!empty($filter_program)) {
    $where[] = "p.nama_program LIKE ?";
    $params[] = "%$filter_program%";
    $types .= 's';
}

// Gabungkan jadi satu string WHERE
$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

// --- Query data dengan filter
$sql = "
  SELECT ab.*,
        k.nama_kategori,
        l.nama_lokasi,
        p.nama_program,
        CONCAT(
          CASE WHEN ab.periode_bulan IS NOT NULL THEN 
            CASE ab.periode_bulan 
              WHEN 1 THEN 'Januari' WHEN 2 THEN 'Februari' WHEN 3 THEN 'Maret' WHEN 4 THEN 'April'
              WHEN 5 THEN 'Mei' WHEN 6 THEN 'Juni' WHEN 7 THEN 'Juli' WHEN 8 THEN 'Agustus'
              WHEN 9 THEN 'September' WHEN 10 THEN 'Oktober' WHEN 11 THEN 'November' WHEN 12 THEN 'Desember'
            END 
          ELSE '' END,
          CASE WHEN ab.periode_bulan IS NOT NULL AND ab.periode_tahun IS NOT NULL THEN ' ' ELSE '' END,
          CASE WHEN ab.periode_tahun IS NOT NULL THEN ab.periode_tahun ELSE '' END
        ) as periode_display
  FROM aset_barang ab
  LEFT JOIN kategori_barang k ON ab.kategori_barang = k.id
  LEFT JOIN lokasi_barang l ON ab.lokasi_barang = l.id
  LEFT JOIN program_pendanaan p ON ab.program_pendanaan = p.id
  {$whereSql}
  ORDER BY ab.periode_tahun DESC, ab.periode_bulan DESC, ab.id DESC
";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}

// --- Cek jika data kosong
if (empty($rows)) {
    die("<script>alert('Tidak ada data untuk diexport dengan filter yang dipilih.'); window.history.back();</script>");
}

// --- Buat PDF dengan TCPDF
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        // Logo PRCF
        $image_file = '../../assets/img/logo.png';
        if (file_exists($image_file)) {
            $this->Image($image_file, 10, 10, 25, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        // Set font
        $this->SetFont('helvetica', 'B', 16);
        // Title
        $this->Cell(0, 15, 'LAPORAN INVENTARIS ASET', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'PRCF INDONESIA', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(15);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Halaman '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        // Tanggal generate
        $this->SetY(-25);
        $this->Cell(0, 10, 'Dicetak pada: '.date('d/m/Y H:i:s'), 0, false, 'L', 0, '', 0, false, 'T', 'M');
    }
}

// Create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('PRCF Indonesia');
$pdf->SetAuthor('Sistem Inventaris PRCF');
$pdf->SetTitle('Laporan Inventaris Aset');
$pdf->SetSubject('Data Aset PRCF');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(10, 40, 10);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(15);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 25);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage('L');

// Set font for content
$pdf->SetFont('helvetica', '', 9);

// --- INFORMASI FILTER DETAIL ---
$filter_info = "DATA TERFILTER: ";

$filters = [];
if (!empty($search)) $filters[] = "Pencarian: '" . htmlspecialchars($search) . "'";
if ($kategori > 0) {
    $kategori_name = mysqli_fetch_assoc($conn->query("SELECT nama_kategori FROM kategori_barang WHERE id = $kategori"));
    $filters[] = "Kategori: " . ($kategori_name['nama_kategori'] ?? '');
}
if ($tahun_filter) $filters[] = "Tahun: " . $tahun_filter;
if ($bulan_filter) {
    $bulan_list = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    $filters[] = "Bulan: " . ($bulan_list[$bulan_filter] ?? $bulan_filter);
}
if (!empty($filter_nama)) $filters[] = "Nama Barang: '" . htmlspecialchars($filter_nama) . "'";
if (!empty($filter_kondisi)) $filters[] = "Kondisi: " . htmlspecialchars($filter_kondisi);
if (!empty($filter_lokasi)) $filters[] = "Lokasi: '" . htmlspecialchars($filter_lokasi) . "'";
if (!empty($filter_program)) $filters[] = "Program: '" . htmlspecialchars($filter_program) . "'";

if (empty($filters)) {
    $filter_info = "SEMUA DATA (Tidak ada filter aktif)";
} else {
    $filter_info .= implode(" | ", $filters);
}

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 8, $filter_info, 0, 1, 'L');
$pdf->Ln(2);

// Jumlah data
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 6, 'Total Data: ' . count($rows) . ' item', 0, 1, 'L');
$pdf->Ln(2);

// Create table header
$header = array('No', 'Nama Barang', 'Kategori', 'Kondisi', 'Lokasi', 'Program', 'Jumlah', 'Harga', 'Periode');

// Set table widths
$widths = array(10, 45, 30, 25, 30, 35, 15, 25, 25);

// Set fill color for header
$pdf->SetFillColor(220, 220, 220);
$pdf->SetTextColor(0);
$pdf->SetLineWidth(0.1);
$pdf->SetFont('helvetica', 'B', 8);

// Header row
for($i = 0; $i < count($header); $i++) {
    $pdf->Cell($widths[$i], 8, $header[$i], 1, 0, 'C', 1);
}
$pdf->Ln();

// Table content
$pdf->SetFont('helvetica', '', 7);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0);

$no = 1;
$total_harga = 0;

foreach($rows as $row) {
    // Check for page break
    if($pdf->GetY() > 170) {
        $pdf->AddPage('L');
        // Print header again
        $pdf->SetFont('helvetica', 'B', 8);
        for($i = 0; $i < count($header); $i++) {
            $pdf->Cell($widths[$i], 8, $header[$i], 1, 0, 'C', 1);
        }
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 7);
    }
    
    $harga = (float)($row['harga_pembelian'] ?? 0);
    $total_harga += $harga;
    
    $pdf->Cell($widths[0], 6, $no, 'LR', 0, 'C', true);
    $pdf->Cell($widths[1], 6, substr($row['nama_barang'], 0, 30), 'LR', 0, 'L', true);
    $pdf->Cell($widths[2], 6, substr($row['nama_kategori'] ?? '-', 0, 15), 'LR', 0, 'L', true);
    $pdf->Cell($widths[3], 6, substr($row['kondisi_barang'], 0, 12), 'LR', 0, 'C', true);
    $pdf->Cell($widths[4], 6, substr($row['nama_lokasi'] ?? '-', 0, 15), 'LR', 0, 'L', true);
    $pdf->Cell($widths[5], 6, substr($row['nama_program'] ?? '-', 0, 20), 'LR', 0, 'L', true);
    $pdf->Cell($widths[6], 6, $row['jumlah_unit'], 'LR', 0, 'C', true);
    $pdf->Cell($widths[7], 6, $harga > 0 ? 'Rp '.number_format($harga, 0, ',', '.') : '-', 'LR', 0, 'R', true);
    $pdf->Cell($widths[8], 6, $row['periode_display'] ?? '-', 'LR', 0, 'C', true);
    $pdf->Ln();
    
    $no++;
}

// Closing line
$pdf->Cell(array_sum($widths), 0, '', 'T');
$pdf->Ln(5);

// Total summary
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(0, 8, 'TOTAL NILAI ASET: Rp ' . number_format($total_harga, 0, ',', '.'), 0, 1, 'R');
$pdf->Cell(0, 6, 'JUMLAH ITEM: ' . count($rows), 0, 1, 'R');

// Close and output PDF
$filename = 'Laporan_Aset_PRCF_' . date('Y-m-d_H-i-s') . '.pdf';
$pdf->Output($filename, 'D');

exit;
?>