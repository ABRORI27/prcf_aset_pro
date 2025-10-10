<?php
session_start();
include 'includes/koneksi.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $pwd = $_POST['password'];
    $stmt = $conn->prepare('SELECT id, password_hash, role FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s',$username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if (password_verify($pwd, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            header('Location: index.php'); exit;
        } else $err = 'Password salah';
    } else $err = 'User tidak ditemukan';
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<div class="login-box">
  <h2>Login</h2>
  <?php if($err) echo '<div class="alert">'.$err.'</div>'; ?>
  <form method="post">
    <label>Username<input name="username" required> <br></label>
    <label>Password<input type="password" name="password" required> <br></label>
    <button class="btn" type="submit">Login</button>
  </form>
</div>
</body></html>