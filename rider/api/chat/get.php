<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if (isset($_SESSION['user_id']) && isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    try {
        $stmt = $pdo->prepare("SELECT m.*, u.name as sender_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.booking_id = ? ORDER BY m.created_at ASC");
        $stmt->execute([$booking_id]);
        $messages = $stmt->fetchAll();
        echo json_encode(['success' => true, 'messages' => $messages]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
}
