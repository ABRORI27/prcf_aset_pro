<?php
include '../../includes/auth_check.php';
include '../../config/db.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Aset Barang - PRCF Indonesia</title>
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
  <style>
    body {
      background: #fff;
      color: #000;
      font-family: Arial, sans-serif;
      margin: 20px;
    }

    h1 {
      text-align: center;
      color: #2b6b4f;
      margin-bottom: 20px;
    }

    .laporan-container {
      background: #fafafa;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 20px;
    }

    .filter-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .filter-bar select, .filter-bar input {
      padding: 6px 10px;
      border-radius: 4px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    .btn {
      background-color: #2b6b4f;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 6px 12px;
      cursor: pointer;
      text-decoration: none;
      font-size: 14px;
    }

    .btn:hover {
      background-color: #1f4e3a;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      background: white;
    }

    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #e8f5e9;
      color: #000;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    @media print {
      .no-print { display: none; }
      body { background: white; color: black; }
      table { border: 1px solid #000; }
      th, td { border: 1px solid #000; }
    }
  </style>
</head>
<body>
  <h1>Laporan Aset Barang PRCF Indonesia</h1>

  <div class="laporan-container">
    <div class="filter-bar no-print">
      <form method="GET">
        <label for="kategori">Kategori:</label>
        <select name="kategori" id="kategori">
          <option value="">Semua</option>
          <?php
          $kategori = mysqli_query($conn, "SELECT * FROM kategori_barang ORDER BY nama_kategori");
          while ($k = mysqli_fetch_assoc($kategori)) {
            $selected = (isset($_GET['kategori']) && $_GET['kategori'] == $k['id']) ? 'selected' : '';
            echo "<option value='{$k['id']}' $selected>{$k['nama_kategori']}</option>";
          }
          ?>
        </select>

        <label for="lokasi">Lokasi:</label>
        <select name="lokasi" id="lokasi">
          <option value="">Semua</option>
          <?php
          $lokasi = mysqli_query($conn, "SELECT * FROM lokasi_barang ORDER BY nama_lokasi");
          while ($l = mysqli_fetch_assoc($lokasi)) {
            $selected = (isset($_GET['lokasi']) && $_GET['lokasi'] == $l['id']) ? 'selected' : '';
            echo "<option value='{$l['id']}' $selected>{$l['nama_lokasi']}</option>";
          }
          ?>
        </select>

        <button type="submit" class="btn">Tampilkan</button>
        <button type="button" class="btn" onclick="window.print()">🖨 Cetak</button>
        <a href="../../index.php" class="btn">⬅ Kembali</a>
      </form>
    </div>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Barang</th>
          <th>Kategori</th>
          <th>Kondisi</th>
          <th>Lokasi</th>
          <th>Program Pendanaan</th>
          <th>Tanggal Input</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $where = [];
        if (!empty($_GET['kategori'])) $where[] = "ab.kategori_barang = '".mysqli_real_escape_string($conn, $_GET['kategori'])."'";
        if (!empty($_GET['lokasi'])) $where[] = "ab.lokasi_barang = '".mysqli_real_escape_string($conn, $_GET['lokasi'])."'";

        $whereSQL = $where ? "WHERE ".implode(" AND ", $where) : "";

        $query = "
          SELECT ab.*, 
                 k.nama_kategori, 
                 l.nama_lokasi, 
                 p.nama_program
          FROM aset_barang ab
          LEFT JOIN kategori_barang k ON ab.kategori_barang = k.id
          LEFT JOIN lokasi_barang l ON ab.lokasi_barang = l.id
          LEFT JOIN program_pendanaan p ON ab.program_pendanaan = p.id
          $whereSQL
          ORDER BY ab.id DESC
        ";

        $result = mysqli_query($conn, $query);
        $no = 1;

        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
              <td>{$no}</td>
              <td>{$row['nama_barang']}</td>
              <td>".($row['nama_kategori'] ?? '-')."</td>
              <td>{$row['kondisi_barang']}</td>
              <td>".($row['nama_lokasi'] ?? '-')."</td>
              <td>".($row['nama_program'] ?? '-')."</td>
              <td>".date('d-m-Y', strtotime($row['created_at'] ?? 'now'))."</td>
            </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='7' style='text-align:center;'>Tidak ada data ditemukan.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
