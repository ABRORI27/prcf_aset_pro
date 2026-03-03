<?php
function logActivity($conn, $user_id, $activity_type) {

    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

    // Deteksi apakah IP lokal
    $is_ip_local = (
        str_starts_with($ip_address, '192.168.') ||
        $ip_address === '127.0.0.1'
    ) ? 1 : 0;

    $stmt = $conn->prepare("
        INSERT INTO user_logs 
        (user_id, activity_type, ip_address, user_agent, is_ip_local)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("isssi", $user_id, $activity_type, $ip_address, $user_agent, $is_ip_local);
    $stmt->execute();
    $stmt->close();
}
?>