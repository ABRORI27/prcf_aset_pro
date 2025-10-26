<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Home - PRCF Indonesia</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
  header.site-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 40px;
    background: #ffffff;
    border-bottom: 1px solid #e0e0e0;
  }
  .logo { height: 60px; width: auto; }
  nav.topnav { display: flex; gap: 24px; }
  nav.topnav a { color: #204c37; font-weight: 600; text-decoration: none; }
  nav.topnav a:hover { color: #2b6b4f; }
  .hero { text-align: center; padding: 100px 20px; background: linear-gradient(180deg, #f8fbf9, #ffffff); }
  .hero h1 { color: #0f2b21; font-size: 2rem; margin-bottom: 10px; }
  .hero p { color: #555; font-size: 1rem; }
  footer.site-footer { text-align: center; padding: 20px; background: #f6f9f7; border-top: 1px solid #e0e0e0; color: #204c37; }
</style>
</head>
<body>
  <header class="site-header">
    <img src="assets/img/logo.png" alt="PRCF Indonesia" class="logo">
    <nav class="topnav">
      <a href="home.php">Home</a>
      <a href="about.php">About</a>
      <a href="login.php">Login</a>
    </nav>
  </header>

  <main class="hero">
    <h1>Selamat Datang di Sistem Internal PRCF Indonesia</h1>
    <p>Kelola data aset dan sumber daya manusia dengan efisien dan transparan.</p>
    <br>
  </main>

  <footer class="site-footer">
    © <?= date('Y') ?> PRCF Indonesia
  </footer>
</body>
</html>
