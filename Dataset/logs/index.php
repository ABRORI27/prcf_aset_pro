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
    JOIN users u ON ul.user_id = u.id
    ORDER BY ul.created_at DESC
";

$result = $conn->query($query);
?>

<h2>DATASET - USER ACTIVITY LOGS</h2>

<table border="1" cellpadding="8">
<tr>
    <th>No</th>
    <th>Username</th>
    <th>Nama</th>
    <th>Aktivitas</th>
    <th>IP</th>
    <th>Waktu</th>
</tr>

<?php 
$no = 1;
while($row = $result->fetch_assoc()) {
?>
<tr>
    <td><?= $no++; ?></td>
    <td><?= $row['username']; ?></td>
    <td><?= $row['nama_lengkap']; ?></td>
    <td><?= $row['aktivitas']; ?></td>
    <td><?= $row['ip_address']; ?></td>
    <td><?= $row['created_at']; ?></td>
</tr>
<?php } ?>
</table>
