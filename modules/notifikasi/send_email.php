<?php

require '../../libraries/PHPMailer/src/Exception.php';
require '../../libraries/PHPMailer/src/PHPMailer.php';
require '../../libraries/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function kirimEmailNotifikasi($email, $nama_barang, $tipe, $tanggal_tenggat)
{
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '15anasanjiabrori@gmail.com'; // GANTI
        $mail->Password   = 'rsgr qjsx ampc xork';  // GANTI (App Password)
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Pengirim
        $mail->setFrom('15anasanjiabrori@gmail.com', 'Sistem Notifikasi Aset PRCF');
        $mail->addAddress($email);

        // Konten
        $mail->isHTML(true);
        $mail->Subject = "Pengingat $tipe - $nama_barang";
        $mail->Body    = "
            <h3>Pengingat Notifikasi Aset</h3>
            <p>Halo,</p>
            <p>Aset <b>$nama_barang</b> memiliki jadwal <b>$tipe</b>.</p>
            <p><b>Tanggal Tenggat:</b> $tanggal_tenggat</p>
            <p>Email ini dikirim otomatis 30 hari sebelum tenggat.</p>
            <br>
            <p>Terima kasih,<br>Sistem Aset PRCF</p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}
