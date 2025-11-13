<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // pastikan sudah install PHPMailer via composer
include 'includes/koneksi.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // cek email di database
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($query) > 0) {
        $token = bin2hex(random_bytes(16)); // buat token unik
        $expired = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        mysqli_query($conn, "UPDATE users SET reset_token='$token', token_expired='$expired' WHERE email='$email'");

        // kirim email reset password
        $mail = new PHPMailer(true);
        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '15anasanjiabrori@gmail.com'; // ubah
            $mail->Password = 'rsgr qjsx ampc xork ';    // ubah (gunakan App Password Gmail)
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email tujuan
            $mail->setFrom('EMAIL_KAMU@gmail.com', 'PRCF Indonesia');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Password - PRCF Indonesia';
            $mail->Body = "
                Halo,<br><br>
                Klik link berikut untuk mengatur ulang password Anda:<br>
                <a href='http://localhost/prcf_aset_pro/reset_password.php?token=$token'>
                Reset Password
                </a><br><br>
                Link ini akan kedaluwarsa dalam 30 menit.
            ";

            $mail->send();
            $message = "<div class='success'>Email reset password telah dikirim ke <b>$email</b>.</div>";
        } catch (Exception $e) {
            $message = "<div class='error'>Gagal mengirim email. Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $message = "<div class='error'>Email tidak ditemukan.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Lupa Password - PRCF Indonesia</title>
<style>
body {
  font-family: Arial, sans-serif;
  background: #e9f0ec;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
}
.container {
  background: white;
  padding: 30px;
  border-radius: 10px;
  width: 380px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
h2 { text-align: center; color: #2b6b4f; }
input[type=email], button {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
}
button {
  background-color: #2b6b4f;
  color: white;
  border: none;
  cursor: pointer;
}
button:hover { background-color: #1e4a38; }
.success { color: green; margin-top: 10px; }
.error { color: red; margin-top: 10px; }
</style>
</head>
<body>
  <div class="container">
    <h2>Lupa Password</h2>
    <form method="POST">
      <label>Masukkan Email Terdaftar</label>
      <input type="email" name="email" required placeholder="contoh@email.com">
      <button type="submit">Kirim Link Reset</button>
    </form>
    <?= $message ?>
  </div>
</body>
</html>
