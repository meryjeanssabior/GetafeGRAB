<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    
    $completed_at_sql = ($status === 'completed') ? ", completed_at = CURRENT_TIMESTAMP" : "";

    try {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? $completed_at_sql WHERE id = ?");
        $stmt->execute([$status, $booking_id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
