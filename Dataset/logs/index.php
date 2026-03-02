<?php
session_start();
include '../../includes/koneksi.php';
include '../../includes/auth_check.php';

if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
    die("Akses hanya untuk Admin");
}

$query = "
    SELECT ul.*, u.username, u.nama_lengkap 
    FROM user_logs ul
    LEFT JOIN users u ON ul.user_id = u.id
    ORDER BY ul.created_at DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs - Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 20px;
        }
        h2 {
            color: #2b6b4f;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #2b6b4f;
            padding-bottom: 10px;
        }
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        /* Header Tabel dengan Background #2b6b4f */
        th {
            background-color: #2b6b4f;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-size: 14px;
        }
        td {
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            color: #333;
        }
        /* Zebra striping */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        /* Style khusus untuk baris log yang Invalid/Stranger */
        .row-invalid {
            background-color: #fff5f5 !important;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-red {
            background-color: #e74c3c;
            color: white;
        }
        .ip-text {
            font-family: monospace;
            color: #666;
            background: #eee;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .ip-text {
    font-family: monospace;
    color: #666;
    background: #eee;
    padding: 2px 4px;
    border-radius: 3px;
    filter: blur(4px);
    transition: filter 0.3s ease;
    cursor: pointer;
}

/* Saat hover → tampil jelas */
.ip-text:hover {
    filter: blur(0);
}
    </style>
</head>
<body>

<div class="table-container">
    <h2>DATASET - USER ACTIVITY LOGS</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Aktivitas & Catatan</th>
                <th>IP Address</th>
                <th>Waktu Kejadian</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $no = 1;
        while($row = $result->fetch_assoc()) {
            // Logika untuk mendeteksi baris invalid (tanpa user_id)
            $is_invalid = empty($row['user_id']);
            $row_class = $is_invalid ? 'class="row-invalid"' : '';
        ?>
            <tr <?= $row_class ?>>
                <td><?= $no++; ?></td>
                <td>
                    <?php if($is_invalid): ?>
                        <span class="status-badge badge-red">Stranger / Invalid</span>
                    <?php else: ?>
                        <strong><?= htmlspecialchars($row['username']); ?></strong>
                    <?php endif; ?>
                </td>
                <td><?= $row['nama_lengkap'] ? htmlspecialchars($row['nama_lengkap']) : '-'; ?></td>
                <td><?= htmlspecialchars($row['aktivitas']); ?></td>
                <td>
                    <span class="ip-text" title="Hover untuk melihat IP lengkap">
                        <?= htmlspecialchars($row['ip_address']); ?>
                    </span>
                </td>
                <td><small><?= date('d M Y, H:i:s', strtotime($row['created_at'])); ?></small></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>