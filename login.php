<?php
session_start();
include 'includes/koneksi.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $pwd = trim($_POST['password']);

    $stmt = $conn->prepare('SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if (password_verify($pwd, $row['password_hash'])) {

            // Simpan semua data user ke session
            $_SESSION['user'] = [
                'id' => $row['id'],
                'username' => $row['username'],
                'role' => $row['role']
            ];

            // Redirect otomatis berdasarkan role
            switch ($row['role']) {
                case 'Admin':
                    header('Location: index.php');
                    break;
                case 'Operator':
                    header('Location: modules/aset/output_aset.php');
                    break;
                case 'Auditor':
                    header('Location: modules/aset/export_excel.php');
                    break;
                default:
                    header('Location: index.php');
                    break;
            }
            exit;

        } else {
            $err = '⚠️ Password salah.';
        }
    } else {
        $err = '⚠️ Username tidak ditemukan.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login - PRCF Indonesia</title>
  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
  <div class="login-box">
    <h2>Login</h2>

    <?php if ($err): ?>
      <div class="alert red" style="margin-bottom:10px;">
        <?= htmlspecialchars($err) ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <label>Username</label>
      <input type="text" name="username" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <button class="btn" type="submit">Login</button>
    </form>
  </div>
</body>
</html>
