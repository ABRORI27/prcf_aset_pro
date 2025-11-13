<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'includes/koneksi.php';

session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='error'>Format email tidak valid.</div>";
    } else {
        // cek email di database
        $query = mysqli_query($conn, "SELECT id, name FROM users WHERE email = '$email'");
        if (mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);
            $token = bin2hex(random_bytes(16));
            $expired = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            
            // Update token
            mysqli_query($conn, "UPDATE users SET reset_token='$token', token_expired='$expired' WHERE email='$email'");

            // kirim email reset password
            $mail = new PHPMailer(true);
            try {
                // SMTP config - SEBAIKNYA PINDAH KE CONFIG FILE
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = '15anasanjiabrori@gmail.com';
                $mail->Password = 'rsgr qjsx ampc xork';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                // Email content
                $mail->setFrom('15anasanjiabrori@gmail.com', 'PRCF Indonesia');
                $mail->addAddress($email, $user['name']);
                $mail->isHTML(true);
                $mail->Subject = 'Reset Password - PRCF Indonesia';
                
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
                
                $mail->Body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                        <h2 style='color: #2b6b4f;'>Reset Password PRCF Indonesia</h2>
                        <p>Halo <b>{$user['name']}</b>,</p>
                        <p>Kami menerima permintaan reset password untuk akun Anda.</p>
                        <p>Klik tombol berikut untuk mengatur ulang password:</p>
                        <p style='text-align: center; margin: 30px 0;'>
                            <a href='$reset_link' style='background-color: #2b6b4f; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;'>
                                Reset Password
                            </a>
                        </p>
                        <p>Atau copy link berikut ke browser Anda:</p>
                        <p style='word-break: break-all; background: #f5f5f5; padding: 10px; border-radius: 4px;'>
                            $reset_link
                        </p>
                        <p><small>Link ini akan kedaluwarsa dalam 30 menit.</small></p>
                        <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
                        <br>
                        <p>Hormat kami,<br>Tim PRCF Indonesia</p>
                    </div>
                ";

                // Tambahkan plain text version
                $mail->AltBody = "Reset Password PRCF Indonesia\n\nHalo {$user['name']},\n\nKlik link berikut untuk reset password: $reset_link\n\nLink berlaku 30 menit.";

                if ($mail->send()) {
                    $message = "<div class='success'>Email reset password telah dikirim ke <b>$email</b>. Periksa folder spam jika tidak ditemukan.</div>";
                }
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
                $message = "<div class='error'>Gagal mengirim email. Silakan coba lagi beberapa saat.</div>";
            }
        } else {
            $message = "<div class='error'>Email tidak ditemukan dalam sistem.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - PRCF Indonesia</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e9f0ec 0%, #d4e8e0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
        h2 {
            text-align: center;
            color: #2b6b4f;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        input[type="email"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        input[type="email"]:focus {
            outline: none;
            border-color: #2b6b4f;
        }
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2b6b4f 0%, #3a8c6d 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(43, 107, 79, 0.3);
        }
        button:active {
            transform: translateY(0);
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
            border: 1px solid #c3e6cb;
            font-size: 14px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
            border: 1px solid #f5c6cb;
            font-size: 14px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #2b6b4f;
            text-decoration: none;
            font-size: 14px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <!-- Tambahkan logo PRCF di sini -->
            <h2 style="color: #2b6b4f; margin: 0;">PRCF INDONESIA</h2>
        </div>
        
        <h2>Lupa Password</h2>
        <p class="subtitle">Masukkan email terdaftar untuk mendapatkan link reset</p>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" required 
                       placeholder="contoh@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            
            <button type="submit">Kirim Link Reset</button>
        </form>
        
        <?= $message ?>
        
        <div class="back-link">
            <a href="login.php">← Kembali ke Login</a>
        </div>
    </div>
</body>
</html>