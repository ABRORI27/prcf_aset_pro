<?php
session_start();
include 'includes/koneksi.php';
include 'Dataset/functions/log_activity.php';

// Cek apakah user login
if (isset($_SESSION['user']['id'])) {

    logActivity(
        $conn,
        $_SESSION['user']['id'],
        "Logout dari sistem"
    );
}

// Hapus session
session_unset();
session_destroy();

header('Location: login.php');
exit;
?>
