<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>About - PRCF Indonesia</title>
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

  .about-section {
    max-width: 900px;
    margin: 60px auto;
    padding: 20px;
    line-height: 1.7;
    color: #333;
  }
  .about-section h1 {
    color: #0f2b21;
    text-align: center;
    margin-bottom: 20px;
  }
  .about-section p {
    margin-bottom: 15px;
  }

  footer.site-footer {
    text-align: center;
    padding: 20px;
    background: #f6f9f7;
    border-top: 1px solid #e0e0e0;
    color: #204c37;
  }
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

  <main class="about-section">
    <h1>Tentang PRCF Indonesia</h1>
    <p><strong>PRCF (People Resources and Conservation Foundation) Indonesia</strong> adalah organisasi nirlaba yang berfokus pada konservasi lingkungan, pengelolaan sumber daya alam berkelanjutan, dan pemberdayaan masyarakat lokal di Kalimantan Barat.</p>

    <p>Melalui berbagai program seperti pelestarian hutan, perlindungan satwa liar, serta peningkatan kapasitas masyarakat, PRCF berkomitmen untuk menjaga keseimbangan antara konservasi dan kesejahteraan manusia.</p>

    <p>Sistem <strong>PRCF Internal</strong> ini merupakan platform untuk membantu manajemen data aset dan kepegawaian secara efisien, transparan, dan terintegrasi.</p>
  </main>

  <footer class="site-footer">
    © <?= date('Y') ?> PRCF Indonesia
  </footer>
</body>
</html>
