<?php
include 'includes/koneksi.php';
session_start();

$token = $_GET['token'] ?? '';
$message = '';
$valid_token = false;

error_log("RESET_PASSWORD - Token received: " . $token);

// Validasi token
if (!empty($token)) {
    $token = mysqli_real_escape_string($conn, $token);
    
    // DEBUG: Cek token di database dengan berbagai kondisi
    $debug_query = mysqli_query($conn, "SELECT id, email, reset_token, token_expired, NOW() as current_time FROM users WHERE reset_token='$token'");
    if ($debug_query && mysqli_num_rows($debug_query) > 0) {
        $debug_data = mysqli_fetch_assoc($debug_query);
        error_log("DEBUG - Token found: " . $debug_data['reset_token'] . ", Expired: " . $debug_data['token_expired'] . ", Current: " . $debug_data['current_time']);
        
        // Cek manual expiry
        $current_time = strtotime($debug_data['current_time']);
        $expired_time = strtotime($debug_data['token_expired']);
        $time_diff = $expired_time - $current_time;
        
        error_log("DEBUG - Time check: Current=" . date('Y-m-d H:i:s', $current_time) . ", Expired=" . date('Y-m-d H:i:s', $expired_time) . ", Diff=" . $time_diff . " seconds");
        
        if ($time_diff > 0) {
            error_log("DEBUG - Token is still valid");
            $valid_token = true;
            $user_data = $debug_data;
        } else {
            error_log("DEBUG - Token has expired");
        }
    } else {
        error_log("DEBUG - Token NOT found in database");
    }
}

// Alternatif validation yang lebih reliable
if (!$valid_token && !empty($token)) {
    // Gunakan PHP untuk validasi waktu, bukan MySQL
    $check_query = mysqli_query($conn, "SELECT id, email, reset_token, token_expired FROM users WHERE reset_token='$token'");
    if ($check_query && mysqli_num_rows($check_query) > 0) {
        $token_data = mysqli_fetch_assoc($check_query);
        $current_time = time();
        $expired_time = strtotime($token_data['token_expired']);
        
        if ($expired_time > $current_time) {
            $valid_token = true;
            $user_data = $token_data;
            error_log("DEBUG - PHP Time Validation: Token VALID");
        } else {
            error_log("DEBUG - PHP Time Validation: Token EXPIRED");
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = mysqli_real_escape_string($conn, $_POST['token']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    error_log("RESET_PASSWORD POST - Token: " . $token);
    
    // Validasi password
    if (empty($password) || empty($confirm_password)) {
        $message = "<div class='error'>Harap isi semua field password.</div>";
    } elseif (strlen($password) < 8) {
        $message = "<div class='error'>Password minimal 8 karakter.</div>";
    } elseif ($password !== $confirm_password) {
        $message = "<div class='error'>Konfirmasi password tidak cocok.</div>";
    } else {
        // Validasi token dengan PHP (lebih reliable)
        $check_query = mysqli_query($conn, "SELECT id, email, reset_token, token_expired FROM users WHERE reset_token='$token'");
        if ($check_query && mysqli_num_rows($check_query) > 0) {
            $token_data = mysqli_fetch_assoc($check_query);
            $current_time = time();
            $expired_time = strtotime($token_data['token_expired']);
            
            if ($expired_time > $current_time) {
                $user_data = $token_data;
                error_log("DEBUG - Resetting password for: " . $user_data['email']);
                
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                
                // Update password dan clear token
                $update_query = mysqli_query($conn, "UPDATE users SET password_hash='$hashed_password', reset_token=NULL, token_expired=NULL WHERE reset_token='$token'");
                
                if ($update_query && mysqli_affected_rows($conn) > 0) {
                    error_log("DEBUG - Password reset successful for: " . $user_data['email']);
                    $_SESSION['success_message'] = "Password berhasil diperbarui. Silakan login dengan password baru.";
                    header('Location: login.php');
                    exit();
                } else {
                    $error_msg = mysqli_error($conn);
                    error_log("DEBUG - Update failed: " . $error_msg);
                    $message = "<div class='error'>Gagal memperbarui password. Error: $error_msg</div>";
                }
            } else {
                $message = "<div class='error'>Token sudah kedaluwarsa.</div>";
                $valid_token = false;
            }
        } else {
            $message = "<div class='error'>Token tidak valid.</div>";
            $valid_token = false;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - PRCF Indonesia</title>
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
        h2 {
            text-align: center;
            color: #2b6b4f;
            margin-bottom: 30px;
            font-weight: 600;
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
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        input[type="password"]:focus {
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
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #2b6b4f;
            text-decoration: none;
        }
        .token-invalid {
            text-align: center;
            padding: 40px 20px;
        }
        .token-invalid h3 {
            color: #721c24;
            margin-bottom: 15px;
        }
        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        
        <!-- Debug info -->
        <?php if (!empty($token)): ?>
        <div class="debug-info">
            <strong>Debug Info:</strong><br>
            Token: <?= substr($token, 0, 10) ?>...<br>
            Status: <?= $valid_token ? 'Valid' : 'Invalid' ?><br>
            <?php if (isset($debug_data)): ?>
            Expired: <?= $debug_data['token_expired'] ?><br>
            Current: <?= $debug_data['current_time'] ?><br>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!$valid_token && empty($message)): ?>
            <div class="token-invalid">
                <h3>Link Tidak Valid</h3>
                <p>Link reset password tidak valid atau sudah kedaluwarsa.</p>
                <p>Silakan request link reset password baru.</p>
                <div class="back-link">
                    <a href="forgot_password.php">Request Link Baru</a>
                </div>
            </div>
        <?php else: ?>
            <?= $message ?>
            
            <?php if ($valid_token): ?>
            <form method="POST" id="resetForm">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Masukkan password baru" minlength="8">
                    <div class="password-requirements">Minimal 8 karakter</div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           placeholder="Ulangi password baru">
                </div>
                
                <button type="submit">Ubah Password</button>
            </form>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="login.php">← Kembali ke Login</a>
            </div>
            
            <?php if ($valid_token): ?>
            <script>
                document.getElementById('resetForm').addEventListener('submit', function(e) {
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (password.length < 8) {
                        alert('Password harus minimal 8 karakter');
                        e.preventDefault();
                        return;
                    }
                    
                    if (password !== confirmPassword) {
                        alert('Konfirmasi password tidak cocok');
                        e.preventDefault();
                        return;
                    }
                });
            </script>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>