<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $booking_id = $_POST['booking_id'];
    $sender_id = $_SESSION['user_id'];
    $message = $_POST['message'];

    try {
        $stmt = $pdo->prepare("INSERT INTO messages (booking_id, sender_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$booking_id, $sender_id, $message]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
}
