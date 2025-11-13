<?php
include '../../includes/auth_check.php';
include '../../config/db.php';
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
      background-color: #f9f9f9 !important; /* latar abu muda terang */
      color: #102a23 !important; /* teks gelap */
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
      background-color: #f5f5f5 !important; /* abu muda untuk header */
      font-weight: bold;
    }

    .table tr:nth-child(even) {
      background-color: #fafafa !important;
    }

    .table tr:hover {
      background-color: #e8f5e9 !important; /* hijau lembut saat hover */
    }

    .icon-btn {
      text-decoration: none;
      font-size: 16px;
      margin-right: 6px;
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
      <a href="../../index.php" class="btn btn-secondary">⬅ Kembali ke Dashboard</a>

      <?php if (has_access(['Admin'])): ?>
        <a href="create.php" class="btn">+ Tambah Aset</a>
      <?php endif; ?>

      <?php if (has_access(['Admin', 'Auditor'])): ?>
        <a href="export.php" class="btn">Export Excel</a>
      <?php endif; ?>

      <input type="text" id="searchInput" onkeyup="filterGlobal()" placeholder="Cari aset...">
    </div>
  </div>

  <div class="table-container">
    <table class="table" id="tabelAset">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Barang<br><select id="filterNama" class="filter-select" onchange="filterColumn(1)"><option value="">Semua</option></select></th>
          <th>Kategori<br><select id="filterKategori" class="filter-select" onchange="filterColumn(2)"><option value="">Semua</option></select></th>
          <th>Kondisi<br><select id="filterKondisi" class="filter-select" onchange="filterColumn(3)"><option value="">Semua</option></select></th>
          <th>Lokasi<br><select id="filterLokasi" class="filter-select" onchange="filterColumn(4)"><option value="">Semua</option></select></th>
          <th>Program Pendanaan<br><select id="filterProgram" class="filter-select" onchange="filterColumn(5)"><option value="">Semua</option></select></th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
<?php
$no = 1;
$query = "
  SELECT ab.*,
        k.nama_kategori,
        l.nama_lokasi,
        p.nama_program
  FROM aset_barang ab
  LEFT JOIN kategori_barang k ON ab.kategori_barang = k.id
  LEFT JOIN lokasi_barang l ON ab.lokasi_barang = l.id
  LEFT JOIN program_pendanaan p ON ab.program_pendanaan = p.id
  ORDER BY ab.id DESC
";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
  echo "<tr>
    <td>{$no}</td>
    <td>{$row['nama_barang']}</td>
    <td>" . ($row['nama_kategori'] ?? '-') . "</td>
    <td>{$row['kondisi_barang']}</td>
    <td>" . ($row['nama_lokasi'] ?? '-') . "</td>
    <td>" . ($row['nama_program'] ?? '-') . "</td>
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
    document.getElementById("filterProgram")
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
  const columns = [1, 2, 3, 4, 5];
  const selects = [
    document.getElementById("filterNama"),
    document.getElementById("filterKategori"),
    document.getElementById("filterKondisi"),
    document.getElementById("filterLokasi"),
    document.getElementById("filterProgram")
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
