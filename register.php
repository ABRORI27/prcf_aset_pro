<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/koneksi.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'operator';
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $email = $_POST['email'] ?? '';

    $check = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $check->bind_param('s', $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $err = '⚠️ Username sudah digunakan.';
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare('
            INSERT INTO users (username, password_hash, role, nama_lengkap, email, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ');
        $stmt->bind_param('sssss', $username, $hashed, $role, $nama_lengkap, $email);

        if ($stmt->execute()) {
            header('Location: login.php?success=1');
            exit;
        } else {
            $err = '❌ Gagal membuat akun: ' . $stmt->error;
        }
    }
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Register - PRCF Indonesia</title>
  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
  <div class="login-box">
    <h2>Create New Account</h2>
    <?php if ($err): ?>
      <div class="alert"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <form method="post">
      <label>Username
        <input type="text" name="username" required>
      </label>

      <label>Password
        <input type="password" name="password" required>
      </label>

      
      <label>Full Name
        <input type="text" name="nama_lengkap">
      </label>
      
      <label>Email Address
        <input type="email" name="email">
      </label>
      <label>Role
        <select name="role">
          <option value="operator">Operator</option>
          <option value="auditor">Auditor</option>
          <option value="admin">Admin</option>
        </select>
      </label>
      
      <button class="btn" type="submit">Register</button>
    </form>

    <div class="link">
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>
</body>
</html>
