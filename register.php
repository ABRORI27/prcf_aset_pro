<?php
// Simple registration - only for admin use. In production restrict access.
include 'includes/koneksi.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $pwd = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (username,password_hash,role) VALUES (?,?,?)');
    $stmt->bind_param('sss',$username,$pwd,$role);
    if ($stmt->execute()) { header('Location: login.php'); exit; }
    else $err = $stmt->error;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Register</title>
<link rel="stylesheet" href="assets/css/dashboard.css"></head><body>
<div class="login-box">
  <h2>Register User</h2>
  <?php if($err) echo '<div class="alert">'.$err.'</div>'; ?>
  <form method="post">
    <label>Username<input name="username" required> <br></label>
    <label>Password<input type="password" name="password" required> <br></label>
    <label>Role
      <select name="role"><option value="admin">Admin</option><option value="operator">Operator</option><option value="auditor">Auditor</option></select>
    </label>
    <button class="btn" type="submit">Buat <br></button>
  </form>
</div>
</body></html>