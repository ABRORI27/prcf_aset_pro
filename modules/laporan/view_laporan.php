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
      flex-wrap: wrap;
      gap: 10px;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .filter-bar label {
      font-weight: bold;
      font-size: 14px;
      margin-right: 4px;
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
  <button type="button" class="btn" onclick="window.print()">🖨 Cetak</button>
        <a href="../../index.php" class="btn">⬅ Kembali</a>
  <div class="laporan-container">
    <div class="filter-bar no-print">
      <form method="GET">
    
        <!-- Nama Barang -->
        <label for="nama_barang">Nama Barang:</label>
        <select name="nama_barang" id="nama_barang">
          <option value="">Semua</option>
          <?php
          $nama = mysqli_query($conn, "SELECT DISTINCT nama_barang FROM aset_barang ORDER BY nama_barang");
          while ($n = mysqli_fetch_assoc($nama)) {
            $selected = (isset($_GET['nama_barang']) && $_GET['nama_barang'] == $n['nama_barang']) ? 'selected' : '';
            echo "<option value='{$n['nama_barang']}' $selected>{$n['nama_barang']}</option>";
          }
          ?>
        </select>

        <!-- Kategori -->
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

        <!-- Kondisi -->
        <label for="kondisi">Kondisi:</label>
        <select name="kondisi" id="kondisi">
          <option value="">Semua</option>
          <option value="Baik" <?= (isset($_GET['kondisi']) && $_GET['kondisi'] == 'Baik') ? 'selected' : '' ?>>Baik</option>
          <option value="Rusak Ringan" <?= (isset($_GET['kondisi']) && $_GET['kondisi'] == 'Rusak Ringan') ? 'selected' : '' ?>>Rusak Ringan</option>
          <option value="Rusak Berat" <?= (isset($_GET['kondisi']) && $_GET['kondisi'] == 'Rusak Berat') ? 'selected' : '' ?>>Rusak Berat</option>
        </select>

        <!-- Lokasi -->
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
       
        <!-- Program Pendanaan -->
        <label for="program">Program:</label>
        <select name="program" id="program">
          <option value="">Semua</option>
          <?php
          $program = mysqli_query($conn, "SELECT * FROM program_pendanaan ORDER BY nama_program");
          while ($p = mysqli_fetch_assoc($program)) {
            $selected = (isset($_GET['program']) && $_GET['program'] == $p['id']) ? 'selected' : '';
            echo "<option value='{$p['id']}' $selected>{$p['nama_program']}</option>";
          }
          ?>
        </select>
        <button type="submit" class="btn">Tampilkan</button>
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
        // === Build filter SQL ===
        $where = [];
        if (!empty($_GET['nama_barang'])) $where[] = "ab.nama_barang = '".mysqli_real_escape_string($conn, $_GET['nama_barang'])."'";
        if (!empty($_GET['kategori'])) $where[] = "ab.kategori_barang = '".mysqli_real_escape_string($conn, $_GET['kategori'])."'";
        if (!empty($_GET['kondisi'])) $where[] = "ab.kondisi_barang = '".mysqli_real_escape_string($conn, $_GET['kondisi'])."'";
        if (!empty($_GET['lokasi'])) $where[] = "ab.lokasi_barang = '".mysqli_real_escape_string($conn, $_GET['lokasi'])."'";
        if (!empty($_GET['program'])) $where[] = "ab.program_pendanaan = '".mysqli_real_escape_string($conn, $_GET['program'])."'";

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
