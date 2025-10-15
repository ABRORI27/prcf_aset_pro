<?php
if (!function_exists('has_access')) {

    if (session_status() == PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user'])) {
        header('Location: /prcf_aset_pro/login.php');
        exit;
    }

    $user_role = $_SESSION['user']['role'];

    function has_access($roles) {
        global $user_role;
        return in_array($user_role, (array)$roles);
    }
}
?>
