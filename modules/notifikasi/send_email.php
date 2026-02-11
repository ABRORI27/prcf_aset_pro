<?php

require '../../PHPMailer/src/Exception.php';
require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function kirimEmailNotifikasi($email, $nama_barang, $tipe, $tanggal_tenggat)
{
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '15anasanjiabrori@gmail.com';
        $mail->Password   = 'rsgr qjsx ampc xork';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('15anasanji@gmail.com', 'Sistem Aset PRCF');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Pengingat $tipe - $nama_barang";

        $mail->Body = "
            <h3>Pengingat Notifikasi Aset</h3>
            <p>Aset: <b>$nama_barang</b></p>
            <p>Tipe: <b>$tipe</b></p>
            <p>Tanggal Tenggat: <b>$tanggal_tenggat</b></p>
            <p>Email ini dikirim otomatis 30 hari sebelum tenggat.</p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Email gagal: " . $mail->ErrorInfo);
        return false;
    }
}
