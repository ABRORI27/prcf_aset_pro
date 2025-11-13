<?php
include 'includes/koneksi.php';
$token = $_GET['token'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $check = mysqli_query($conn, "SELECT * FROM users WHERE reset_token='$token' AND token_expired > NOW()");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE users SET password='$password', reset_token=NULL, token_expired=NULL WHERE reset_token='$token'");
        $message = "<div class='success'>Password berhasil diperbarui. Silakan <a href='login.php'>login</a>.</div>";
    } else {
        $message = "<div class='error'>Token tidak valid atau sudah kedaluwarsa.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
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
input[type=password], button {
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
    <h2>Reset Password</h2>
    <form method="POST">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <label>Password Baru</label>
      <input type="password" name="password" required>
      <button type="submit">Ubah Password</button>
    </form>
    <?= $message ?>
  </div>
</body>
</html>
