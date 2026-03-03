<?php
session_start();
include 'includes/koneksi.php';
include 'Dataset/functions/log_activity.php';

if (isset($_SESSION['user']['id'])) {

    logActivity(
        $conn,
        $_SESSION['user']['id'],
        "LOGOUT"
    );
}

session_unset();
session_destroy();

header('Location: login.php');
exit;
?>