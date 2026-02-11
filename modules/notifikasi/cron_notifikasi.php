<?php
include '../../config/db.php';
include 'send_email.php';

$today = date('Y-m-d');

$query = mysqli_query($conn, "
    SELECT n.*, a.nama_barang, a.tanggal_pajak, a.waktu_perolehan,
           a.periode_bulan, u.email
    FROM notifikasi n
    JOIN aset_barang a ON n.aset_id = a.id
    LEFT JOIN users u ON a.user_input = u.id
    WHERE n.status = 'Belum Terkirim'
");

while ($row = mysqli_fetch_assoc($query)) {

    if ($row['tipe_notifikasi'] == "Pajak Kendaraan") {
        $tenggat = $row['tanggal_pajak'];
    } elseif ($row['tipe_notifikasi'] == "Perawatan" && $row['periode_bulan'] != null) {
        $tenggat = date('Y-m-d', strtotime(
            $row['waktu_perolehan'] . ' +' . $row['periode_bulan'] . ' months'
        ));
    } else {
        $tenggat = $row['tanggal_notifikasi'];
    }

    $reminder = date('Y-m-d', strtotime($tenggat . ' -30 days'));

    if ($today >= $reminder && !empty($row['email'])) {

        if (kirimEmailNotifikasi($row['email'], $row['nama_barang'], $row['tipe_notifikasi'], $tenggat)) {

            mysqli_query($conn, "
                UPDATE notifikasi
                SET status = 'Terkirim'
                WHERE id = '{$row['id']}'
            ");
        }
    }
}
