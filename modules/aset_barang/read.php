<?php
include '../../includes/auth_check.php';
include '../../config/db.php';

// Ambil parameter filter periode dari URL
$tahun_filter = $_GET['tahun'] ?? '';
$bulan_filter = $_GET['bulan'] ?? '';
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Data Aset - PRCF Indonesia</title>
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* === Mode Terang Paksa untuk Halaman Ini === */
    body, .page {
      background-color: #f9f9f9 !important;
      color: #102a23 !important;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .header {
      background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
      color: #102a23 !important;
      border-bottom: 2px solid #e9ecef;
      padding: 15px 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .header-left {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .header-left h2 {
      color: #2b6b4f !important;
      margin: 0;
      font-size: 1.5rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .header-left h2 i {
      color: #2b6b4f;
      font-size: 1.3rem;
    }

    .actions {
      display: flex;
      gap: 12px;
      align-items: center;
      flex-wrap: wrap;
    }

    /* Filter Periode */
    .filter-periode {
      display: flex;
      gap: 8px;
      align-items: center;
      background: #fff;
      padding: 8px 15px;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .filter-periode select {
      padding: 6px 10px;
      border-radius: 6px;
      border: 1px solid #ddd;
      background: white;
      font-size: 13px;
      min-width: 120px;
    }

    .filter-periode button {
      background: #2b6b4f;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 6px 12px;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .filter-periode button:hover {
      background: #1f4e3a;
      transform: translateY(-1px);
    }

    /* Tombol Aksi */
    .btn {
      background: linear-gradient(135deg, #2b6b4f 0%, #3a8c6d 100%);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 15px;
      text-decoration: none;
      font-size: 14px;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 2px 5px rgba(43, 107, 79, 0.3);
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(43, 107, 79, 0.4);
      background: linear-gradient(135deg, #1f4e3a 0%, #2b6b4f 100%);
    }

    .btn-secondary {
      background: linear-gradient(135deg, #6c757d 0%, #868e96 100%);
      box-shadow: 0 2px 5px rgba(108, 117, 125, 0.3);
    }

    .btn-secondary:hover {
      background: linear-gradient(135deg, #545b62 0%, #6c757d 100%);
      box-shadow: 0 4px 8px rgba(108, 117, 125, 0.4);
    }

    .btn-icon {
      padding: 10px 12px !important;
      border-radius: 50% !important;
      width: 42px;
      height: 42px;
      justify-content: center;
    }

    .btn-icon .btn-text {
      display: none;
    }

    /* Input search */
    .search-container {
      position: relative;
      display: flex;
      align-items: center;
    }

    #searchInput {
      padding: 8px 12px 8px 35px;
      border-radius: 20px;
      border: 1px solid #ddd;
      font-size: 14px;
      color: #102a23;
      background-color: #fff;
      width: 200px;
      transition: all 0.3s;
    }

    #searchInput:focus {
      width: 250px;
      border-color: #2b6b4f;
      box-shadow: 0 0 0 2px rgba(43, 107, 79, 0.1);
    }

    .search-icon {
      position: absolute;
      left: 12px;
      color: #6c757d;
      z-index: 2;
    }

    /* === Filter Dropdown === */
    .filter-select {
      width: 100%;
      padding: 6px;
      border-radius: 6px;
      border: 1px solid #ddd;
      font-size: 12px;
      background-color: #fff;
      color: #000;
      transition: border-color 0.3s;
    }

    .filter-select:focus {
      border-color: #2b6b4f;
      outline: none;
    }

    th {
      vertical-align: middle;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
      font-weight: 600;
    }

    /* === TABEL TERANG === */
    .table-container {
      background-color: #ffffff !important;
      color: #102a23 !important;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      padding: 20px;
      margin-top: 20px;
      overflow: hidden;
    }

    .table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      background: #fff !important;
      border-radius: 8px;
      overflow: hidden;
    }

    .table th, .table td {
      border: 1px solid #e0e0e0 !important;
      color: #000 !important;
      background-color: #fff !important;
      padding: 12px 8px;
      text-align: left;
    }

    .table th {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
      font-weight: 600;
      border-bottom: 2px solid #2b6b4f !important;
    }

    .table tr:nth-child(even) {
      background-color: #fafafa !important;
    }

    .table tr:hover {
      background-color: #e8f5e9 !important;
      transform: translateY(-1px);
      transition: all 0.2s;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .icon-btn {
      text-decoration: none;
      font-size: 16px;
      margin-right: 8px;
      padding: 6px;
      border-radius: 4px;
      transition: all 0.3s;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
    }

    .icon-btn:hover {
      background-color: #e9ecef;
      transform: scale(1.1);
    }

    .edit-btn { color: #28a745; }
    .delete-btn { color: #dc3545; }
    .detail-btn { color: #17a2b8; }

    .periode-info {
      background: linear-gradient(135deg, #e8f5e9 0%, #d4edda 100%);
      padding: 12px 20px;
      border-radius: 8px;
      margin: 15px 0;
      font-weight: 600;
      color: #155724;
      border-left: 4px solid #28a745;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .no-data {
      text-align: center;
      padding: 40px;
      color: #6c757d;
    }

    .no-data i {
      font-size: 3rem;
      margin-bottom: 15px;
      opacity: 0.5;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .header {
        flex-direction: column;
        gap: 15px;
        padding: 15px;
      }

      .header-left {
        justify-content: center;
        width: 100%;
      }

      .actions {
        justify-content: center;
        width: 100%;
      }

      .filter-periode {
        flex-wrap: wrap;
        justify-content: center;
      }

      #searchInput {
        width: 150px;
      }

      #searchInput:focus {
        width: 180px;
      }
    }
  </style>
</head>
<body>
<div class="page">
  <div class="header">
    <!-- Kiri: Tombol Kembali dan Judul -->
    <div class="header-left">
      <a href="../../index.php" class="btn btn-secondary btn-icon" title="Kembali ke Dashboard">
        <i class="fas fa-arrow-left"></i>
        <span class="btn-text">Kembali</span>
      </a>
      <h2>
        <i class="fas fa-boxes"></i>
        Data Aset
      </h2>
    </div>

    <!-- Kanan: Aksi dan Filter -->
    <div class="actions">
      <!-- Filter Periode -->
      <form method="GET" class="filter-periode">
        <select name="tahun" id="filterTahun">
          <option value="">📅 Semua Tahun</option>
          <?php
          // Ambil tahun unik dari database
          $tahun_query = $conn->query("SELECT DISTINCT periode_tahun FROM aset_barang WHERE periode_tahun IS NOT NULL ORDER BY periode_tahun DESC");
          while ($tahun = $tahun_query->fetch_assoc()) {
            $selected = $tahun_filter == $tahun['periode_tahun'] ? 'selected' : '';
            echo "<option value='{$tahun['periode_tahun']}' $selected>{$tahun['periode_tahun']}</option>";
          }
          ?>
        </select>

        <select name="bulan" id="filterBulan">
          <option value="">📊 Semua Bulan</option>
          <?php
          $bulan_list = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
          ];
          foreach ($bulan_list as $num => $nama) {
            $selected = $bulan_filter == $num ? 'selected' : '';
            echo "<option value='$num' $selected>$nama</option>";
          }
          ?>
        </select>

        <button type="submit" title="Terapkan Filter">
          <i class="fas fa-filter"></i>
          Filter
        </button>
        
        <?php if ($tahun_filter || $bulan_filter): ?>
          <a href="read.php" class="btn btn-secondary btn-icon" title="Reset Filter">
            <i class="fas fa-times"></i>
          </a>
        <?php endif; ?>
      </form>

      <!-- Search -->
      <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Cari aset..." title="Cari berdasarkan nama, kode barang, nomor seri, kategori, lokasi...">
      </div>

      <!-- Tombol Aksi -->
      <?php if (has_access(['admin'])): ?>
        <a href="create.php" class="btn btn-icon" title="Tambah Aset Baru">
          <i class="fas fa-plus"></i>
          <span class="btn-text">Tambah</span>
        </a>
      <?php endif; ?>

      <?php if (has_access(['admin', 'auditor'])): 
        // Build query string untuk filter
        $export_params = [];
        if ($tahun_filter) $export_params['tahun'] = $tahun_filter;
        if ($bulan_filter) $export_params['bulan'] = $bulan_filter;
        
        $export_params = array_merge($export_params, array_filter([
            'search' => $_GET['search'] ?? '',
            'kategori' => $_GET['kategori'] ?? '',
            'nama' => $_GET['nama'] ?? '',
            'kondisi' => $_GET['kondisi'] ?? '',
            'lokasi' => $_GET['lokasi'] ?? '',
            'program' => $_GET['program'] ?? ''
        ]));
        $export_query = http_build_query($export_params);
      ?>
        <a href="export.php?<?= $export_query ?>" class="btn btn-icon" title="Export ke PDF">
          <i class="fas fa-file-pdf"></i>
          <span class="btn-text">Export PDF</span>
        </a>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($tahun_filter || $bulan_filter): ?>
    <div class="periode-info">
      <i class="fas fa-filter"></i>
      Menampilkan data: 
      <?= $bulan_filter ? $bulan_list[$bulan_filter] . ' ' : '' ?>
      <?= $tahun_filter ?: '' ?>
      <?= !$tahun_filter && !$bulan_filter ? 'Semua Periode' : '' ?>
    </div>
  <?php endif; ?>

  <div class="table-container">
    <table class="table" id="tabelAset">
      <thead>
        <tr>
          <th width="50">No</th>
          <th>Nama Barang<br><select id="filterNama" class="filter-select" onchange="filterColumn(1)"><option value="">🔍 Semua</option></select></th>
          <th style="display:none;">Kode Barang</th>
          <th style="display:none;">Nomor Seri</th>
          <th>Kategori<br><select id="filterKategori" class="filter-select" onchange="filterColumn(2)"><option value="">📂 Semua</option></select></th>
          <th>Kondisi<br><select id="filterKondisi" class="filter-select" onchange="filterColumn(3)"><option value="">🔄 Semua</option></select></th>
          <th>Lokasi<br><select id="filterLokasi" class="filter-select" onchange="filterColumn(4)"><option value="">🏢 Semua</option></select></th>
          <th>Program<br><select id="filterProgram" class="filter-select" onchange="filterColumn(5)"><option value="">📋 Semua</option></select></th>
          <th>Periode<br><select id="filterPeriode" class="filter-select" onchange="filterColumn(6)"><option value="">📅 Semua</option></select></th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Build query dengan filter periode
        $where_conditions = [];
        $params = [];
        $types = '';

        if ($tahun_filter) {
            $where_conditions[] = "ab.periode_tahun = ?";
            $params[] = $tahun_filter;
            $types .= 'i';
        }

        if ($bulan_filter) {
            $where_conditions[] = "ab.periode_bulan = ?";
            $params[] = $bulan_filter;
            $types .= 'i';
        }

        $where_sql = '';
        if (!empty($where_conditions)) {
            $where_sql = "WHERE " . implode(' AND ', $where_conditions);
        }

        $query = "
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
          $where_sql
          ORDER BY ab.periode_tahun DESC, ab.periode_bulan DESC, ab.id DESC
        ";

        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $no = 1;
        if ($result->num_rows > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
              <td>{$no}</td>
              <td><strong>{$row['nama_barang']}</strong></td>
                <!-- Kolom hidden untuk pencarian -->
                <td style='display:none;'>{$row['kode_barang']}</td>
                <td style='display:none;'>{$row['nomor_seri']}</td>
              <td>" . ($row['nama_kategori'] ?? '-') . "</td>
              <td>
                <span style='padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; " . 
                getConditionStyle($row['kondisi_barang']) . "'>
                  {$row['kondisi_barang']}
                </span>
              </td>
              <td>" . ($row['nama_lokasi'] ?? '-') . "</td>
              <td>" . ($row['nama_program'] ?? '-') . "</td>
              <td>" . ($row['periode_display'] ?? '-') . "</td>
              <td class='aksi-ikon' style='text-align: center;'>";

            if (has_access(['admin', 'operator'])) {
              echo "
                <a href='update.php?id={$row['id']}' class='icon-btn edit-btn' title='Edit Data'>
                  <i class='fas fa-edit'></i>
                </a>
                <a href='delete.php?id={$row['id']}' class='icon-btn delete-btn' title='Hapus Data' onclick='return confirm(\"Hapus data ini?\")'>
                  <i class='fas fa-trash'></i>
                </a>
              ";
            }

            if (has_access(['admin', 'auditor'])) {
              echo "<a href='detail.php?id={$row['id']}' class='icon-btn detail-btn' title='Lihat Detail'>
                      <i class='fas fa-eye'></i>
                    </a>";
            }

            echo "</td></tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='8'>
                  <div class='no-data'>
                    <i class='fas fa-inbox'></i>
                    <h3>Tidak ada data aset</h3>
                    <p>Data aset tidak ditemukan dengan filter yang dipilih</p>
                  </div>
                </td></tr>";
        }

        // Fungsi untuk styling kondisi barang
        function getConditionStyle($kondisi) {
          $styles = [
            'Baik' => 'background: #d4edda; color: #155724;',
            'Rusak' => 'background: #f8d7da; color: #721c24;',
            'Rusak-perlu diservis, butuh dana besar' => 'background: #fff3cd; color: #856404;',
            'Rusak-kaki kursi patah' => 'background: #ffeaa7; color: #856404;',
            'Hilang' => 'background: #e2e3e5; color: #383d41;',
            'Rusak-Habis Masa Pakai' => 'background: #f5c6cb; color: #721c24;',
            'Rusak-Sedang Diservis' => 'background: #cce7ff; color: #004085;',
            'Kurang baik-Perlu diservis' => 'background: #fff3cd; color: #856404;'
          ];
          return $styles[$kondisi] ?? 'background: #f8f9fa; color: #6c757d;';
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function filterGlobal() {
  const input = document.getElementById("searchInput");
  const filter = input.value.toLowerCase();
  const table = document.getElementById("tabelAset");
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    const row = rows[i];
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? "" : "none";
  }
}

function filterColumn() {
  const table = document.getElementById("tabelAset");
  const rows = table.getElementsByTagName("tr");
  const selects = [
    document.getElementById("filterNama"),
    document.getElementById("filterKategori"),
    document.getElementById("filterKondisi"),
    document.getElementById("filterLokasi"),
    document.getElementById("filterProgram"),
    document.getElementById("filterPeriode")
  ];

  for (let i = 1; i < rows.length; i++) {
    let show = true;
    for (let j = 0; j < selects.length; j++) {
      const filterVal = selects[j].value.toLowerCase();
      const cell = rows[i].getElementsByTagName("td")[j + 1];
      if (filterVal && (!cell || !cell.textContent.toLowerCase().includes(filterVal))) {
        show = false;
        break;
      }
    }
    rows[i].style.display = show ? "" : "none";
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const table = document.getElementById("tabelAset");
  const rows = table.getElementsByTagName("tr");
  const columns = [1, 2, 3, 4, 5, 6];
  const selects = [
    document.getElementById("filterNama"),
    document.getElementById("filterKategori"),
    document.getElementById("filterKondisi"),
    document.getElementById("filterLokasi"),
    document.getElementById("filterProgram"),
    document.getElementById("filterPeriode")
  ];

  columns.forEach((col, idx) => {
    let uniqueValues = new Set();
    for (let i = 1; i < rows.length; i++) {
      const cell = rows[i].getElementsByTagName("td")[col];
      if (cell) {
        const text = cell.textContent.trim();
        if (text && text !== '-') uniqueValues.add(text);
      }
    }

    [...uniqueValues].sort().forEach(value => {
      const option = document.createElement("option");
      option.value = value;
      option.textContent = value;
      selects[idx].appendChild(option);
    });
  });

  // Enter key untuk search
  document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      filterGlobal();
    }
  });
});
</script>

</body>
</html>