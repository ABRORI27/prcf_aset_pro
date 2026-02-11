<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../config/db.php';
include 'send_email.php';

$tanggal_hari_ini = date('Y-m-d');

$sql = "
SELECT n.id, n.tipe_notifikasi, n.tanggal_notifikasi,
    a.nama_barang, a.tanggal_pajak, a.waktu_perolehan,
    a.periode_bulan, u.email
FROM notifikasi n
LEFT JOIN aset_barang a ON n.aset_id = a.id
LEFT JOIN users u ON a.user_input = u.id
WHERE n.status = 'Belum Terkirim'
";

$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {

    $tanggal_tenggat = $row['tanggal_notifikasi'];

    if ($row['tipe_notifikasi'] == "Pajak Kendaraan") {
        $tanggal_tenggat = $row['tanggal_pajak'];

    } elseif ($row['tipe_notifikasi'] == "Perawatan" && !empty($row['periode_bulan'])) {
        $tanggal_tenggat = date('Y-m-d', strtotime(
            $row['waktu_perolehan'] . ' +' . $row['periode_bulan'] . ' months'
        ));
    }

    $tanggal_reminder = date('Y-m-d', strtotime($tanggal_tenggat . ' -30 days'));

    if ($tanggal_hari_ini >= $tanggal_reminder && !empty($row['email'])) {

        if (kirimEmailNotifikasi(
            $row['email'],
            $row['nama_barang'],
            $row['tipe_notifikasi'],
            $tanggal_tenggat
        )) {

            mysqli_query($conn, "
                UPDATE notifikasi
                SET status='Terkirim'
                WHERE id='{$row['id']}'
            ");

            echo "Email terkirim untuk: " . $row['nama_barang'] . "<br>";
        }
    }
}
