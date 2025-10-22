<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// === koneksi database ===
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_prcf";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// === logic insert data ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];

    // validasi sederhana
    if (empty($username) || empty($password)) {
        echo "<p style='color:red'>⚠️ Username dan Password wajib diisi.</p>";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO users (username, password_hash, role, nama_lengkap, email, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("sssss", $username, $hashed, $role, $nama_lengkap, $email);

        if ($stmt->execute()) {
            echo "<p style='color:green'>✅ Data berhasil disimpan ke tabel users!</p>";
        } else {
            echo "<p style='color:red'>❌ Gagal menyimpan data: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Test Insert User</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #111;
      color: #eee;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    form {
      background: #222;
      padding: 20px;
      border-radius: 10px;
      width: 320px;
    }
    input, select {
      width: 100%;
      margin-bottom: 10px;
      padding: 8px;
      border-radius: 5px;
      border: none;
    }
    button {
      background: #0d6efd;
      color: white;
      border: none;
      padding: 10px;
      width: 100%;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background: #0b5ed7;
    }
  </style>
</head>
<body>

  <form method="post">
    <h3>🧩 Test Insert ke Tabel Users</h3>

    <label>Username</label>
    <input type="text" name="username" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Role</label>
    <select name="role">
      <option value="operator">Operator</option>
      <option value="auditor">Auditor</option>
      <option value="admin">Admin</option>
    </select>

    <label>Nama Lengkap</label>
    <input type="text" name="nama_lengkap">

    <label>Email</label>
    <input type="email" name="email">

    <button type="submit">Insert Data</button>
  </form>

</body>
</html>
