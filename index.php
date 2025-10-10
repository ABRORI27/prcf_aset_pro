<?php
session_start();
include 'includes/auth_check.php';
include 'includes/header.php';
?>

<section class="dashboard-hero">
  <h2>Dashboard</h2>
</section>

<section class="grid">
  <div class="card">
    <h3>Aset</h3>
    <p><a class="btn" href="modules/aset/output_aset.php">Kelola Aset</a></p>
  </div>
  <div class="card">
    <h3>HR</h3>
    <p><a class="btn" href="modules/hr/output_employee.php">Kelola Pegawai</a></p>
  </div>
  <div class="card reminder-card">
    <h3>Reminder Pajak</h3>
    <div class="reminder-content">
      <?php include 'reminder.php'; ?>
    </div>
  </div>
</section>


<?php include 'includes/footer.php'; ?>