<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'driver') {
    try {
        $stmt = $pdo->query("SELECT * FROM bookings WHERE status = 'pending' ORDER BY created_at DESC");
        $bookings = $stmt->fetchAll();
        echo json_encode(['success' => true, 'bookings' => $bookings]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
}
?>
