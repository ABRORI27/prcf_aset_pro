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
  <style>
    /* === Mode Terang Paksa untuk Halaman Ini === */
    body, .page {
      background-color: #f9f9f9 !important;
      color: #102a23 !important;
    }

    .header {
      background-color: #ffffff !important;
      color: #102a23 !important;
      border-bottom: 2px solid #ddd;
      padding: 12px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 6px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .header-left h2 {
      color: #102a23 !important;
      margin: 0;
    }

    .actions {
      display: flex;
      gap: 8px;
      align-items: center;
      flex-wrap: wrap;
    }

    /* Filter Periode */
    .filter-periode {
      display: flex;
      gap: 10px;
      align-items: center;
      background: #fff;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #ddd;
    }

    .filter-periode select, .filter-periode button {
      padding: 6px 10px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }

    .filter-periode button {
      background: #2b6b4f;
      color: white;
      border: none;
      cursor: pointer;
    }

    .filter-periode button:hover {
      background: #1f4e3a;
    }

    /* Tombol */
    .btn {
      background-color: #2b6b4f;
      color: #fff;
      border: none;
      border-radius: 4px;
      padding: 6px 12px;
      text-decoration: none;
      font-size: 14px;
      transition: background 0.3s;
    }
    .btn:hover { background-color: #1f4e3a; }

    .btn-secondary {
      background-color: #ccc;
      color: #000;
    }
    .btn-secondary:hover {
      background-color: #aaa;
    }

    /* Input search */
    #searchInput {
      padding: 6px 10px;
      border-radius: 4px;
      border: 1px solid #bbb;
      font-size: 14px;
      color: #102a23;
      background-color: #fff;
    }

    /* === Filter Dropdown === */
    .filter-select {
      width: 100%;
      padding: 4px;
      border-radius: 4px;
      border: 1px solid #ccc;
      font-size: 13px;
      background-color: #fff;
      color: #000;
    }
    th { vertical-align: bottom; }

    /* === TABEL TERANG === */
    .table-container {
      background-color: #ffffff !important;
      color: #102a23 !important;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
      padding: 10px;
      margin-top: 20px;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      background: #fff !important;
    }

    .table th, .table td {
      border: 1px solid #ccc !important;
      color: #000 !important;
      background-color: #fff !important;
      padding: 8px;
      text-align: left;
    }

    .table th {
      background-color: #f5f5f5 !important;
      font-weight: bold;
    }

    .table tr:nth-child(even) {
      background-color: #fafafa !important;
    }

    .table tr:hover {
      background-color: #e8f5e9 !important;
    }

    .icon-btn {
      text-decoration: none;
      font-size: 16px;
      margin-right: 6px;
    }

    .periode-info {
      background: #e8f5e9;
      padding: 8px 12px;
      border-radius: 4px;
      margin: 10px 0;
      font-weight: bold;
      color: #2b6b4f;
    }
  </style>
</head>
<body>
<div class="page">
  <div class="header">
    <div class="header-left">
      <h2>Data Aset</h2>
    </div>

    <div class="actions">
      <!-- Filter Periode -->
      <form method="GET" class="filter-periode">
        <select name="tahun" id="filterTahun">
          <option value="">Semua Tahun</option>
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
          <option value="">Semua Bulan</option>
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

        <button type="submit">Filter</button>
        <?php if ($tahun_filter || $bulan_filter): ?>
          <a href="read.php" class="btn btn-secondary">Reset</a>
        <?php endif; ?>
      </form>

      <a href="../../index.php" class="btn btn-secondary">⬅ Kembali ke Dashboard</a>

      <?php if (has_access(['Admin'])): ?>
        <a href="create.php" class="btn">+ Tambah Aset</a>
      <?php endif; ?>

      <?php if (has_access(['Admin', 'Auditor'])): ?>
        <a href="export.php?tahun=<?= $tahun_filter ?>&bulan=<?= $bulan_filter ?>" class="btn">Export PDF</a>
      <?php endif; ?>

      <input type="text" id="searchInput" onkeyup="filterGlobal()" placeholder="Cari aset...">
    </div>
  </div>

  <?php if ($tahun_filter || $bulan_filter): ?>
    <div class="periode-info">
      🔍 Menampilkan data: 
      <?= $bulan_filter ? $bulan_list[$bulan_filter] . ' ' : '' ?>
      <?= $tahun_filter ?: '' ?>
      <?= !$tahun_filter && !$bulan_filter ? 'Semua Periode' : '' ?>
    </div>
  <?php endif; ?>

  <div class="table-container">
    <table class="table" id="tabelAset">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Barang<br><select id="filterNama" class="filter-select" onchange="filterColumn(1)"><option value="">Semua</option></select></th>
          <th>Kategori<br><select id="filterKategori" class="filter-select" onchange="filterColumn(2)"><option value="">Semua</option></select></th>
          <th>Kondisi<br><select id="filterKondisi" class="filter-select" onchange="filterColumn(3)"><option value="">Semua</option></select></th>
          <th>Lokasi<br><select id="filterLokasi" class="filter-select" onchange="filterColumn(4)"><option value="">Semua</option></select></th>
          <th>Program<br><select id="filterProgram" class="filter-select" onchange="filterColumn(5)"><option value="">Semua</option></select></th>
          <th>Periode<br><select id="filterPeriode" class="filter-select" onchange="filterColumn(6)"><option value="">Semua</option></select></th>
          <th>Aksi</th>
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
while ($row = mysqli_fetch_assoc($result)) {
  echo "<tr>
    <td>{$no}</td>
    <td>{$row['nama_barang']}</td>
    <td>" . ($row['nama_kategori'] ?? '-') . "</td>
    <td>{$row['kondisi_barang']}</td>
    <td>" . ($row['nama_lokasi'] ?? '-') . "</td>
    <td>" . ($row['nama_program'] ?? '-') . "</td>
    <td>" . ($row['periode_display'] ?? '-') . "</td>
    <td class='aksi-ikon'>";

  if (has_access(['Admin', 'Operator'])) {
    echo "
      <a href='update.php?id={$row['id']}' class='icon-btn' title='Edit'>✏️</a>
      <a href='delete.php?id={$row['id']}' class='icon-btn' title='Hapus' onclick='return confirm(\"Hapus data ini?\")'>🗑️</a>
    ";
  }

  if (has_access(['Admin', 'Auditor'])) {
    echo "<a href='detail.php?id={$row['id']}' class='icon-btn' title='Detail'>🔍</a>";
  }

  echo "</td></tr>";
  $no++;
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
  const columns = [1, 2, 3, 4, 5, 6]; // Tambah kolom 6 untuk periode
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
});
</script>

</body>
</html>