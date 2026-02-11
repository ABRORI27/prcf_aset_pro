<?php
include '../../includes/header.php';
include '../../config/db.php';
include 'send_email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $aset_id = $_POST['aset_id'];
    $tipe_notifikasi = $_POST['tipe_notifikasi'];
    $tanggal_notifikasi = $_POST['tanggal_notifikasi'];
    $status = $_POST['status'] ?? 'Belum Terkirim';

    if ($aset_id == '' || $tipe_notifikasi == '' || $tanggal_notifikasi == '') {

        echo "<div class='alert red'>❌ Semua field wajib diisi!</div>";

    } else {

        $sql = "INSERT INTO notifikasi 
                (aset_id, tipe_notifikasi, tanggal_notifikasi, status)
                VALUES 
                ('$aset_id', '$tipe_notifikasi', '$tanggal_notifikasi', '$status')";

        if (mysqli_query($conn, $sql)) {

            // ==============================
            // 🔔 LOGIKA H-30 EMAIL
            // ==============================

            $getAset = mysqli_query($conn, "
                SELECT a.nama_barang, a.tanggal_pajak, a.waktu_perolehan,
                       a.periode_bulan, u.email
                FROM aset_barang a
                LEFT JOIN users u ON a.user_input = u.id
                WHERE a.id = '$aset_id'
            ");

            $asetData = mysqli_fetch_assoc($getAset);

            if ($asetData) {

                $nama_barang = $asetData['nama_barang'];
                $email = $asetData['email'];

                if ($tipe_notifikasi == "Pajak Kendaraan") {
                    $tanggal_tenggat = $asetData['tanggal_pajak'];

                } elseif ($tipe_notifikasi == "Perawatan" && !empty($asetData['periode_bulan'])) {

                    $tanggal_tenggat = date('Y-m-d', strtotime(
                        $asetData['waktu_perolehan'] . ' +' . $asetData['periode_bulan'] . ' months'
                    ));

                } else {
                    $tanggal_tenggat = $tanggal_notifikasi;
                }

                $tanggal_reminder = date('Y-m-d', strtotime($tanggal_tenggat . ' -30 days'));
                $tanggal_sekarang = date('Y-m-d');

                if ($tanggal_sekarang >= $tanggal_reminder && !empty($email)) {

                    if (kirimEmailNotifikasi($email, $nama_barang, $tipe_notifikasi, $tanggal_tenggat)) {

                        mysqli_query($conn, "
                            UPDATE notifikasi 
                            SET status='Terkirim'
                            WHERE aset_id='$aset_id'
                            ORDER BY id DESC LIMIT 1
                        ");
                    }
                }
            }

            echo "<script>alert('✅ Notifikasi berhasil ditambahkan!');window.location='read.php';</script>";

        } else {
            echo "<div class='alert red'>❌ Gagal menambah notifikasi: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>
