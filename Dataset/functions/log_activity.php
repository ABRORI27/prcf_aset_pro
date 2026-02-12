<?php
function logActivity($conn, $user_id, $aktivitas) {

    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

    $stmt = $conn->prepare("
        INSERT INTO user_logs (user_id, aktivitas, ip_address, user_agent)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("isss", $user_id, $aktivitas, $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
}
