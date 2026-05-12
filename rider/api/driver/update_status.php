<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && $_SESSION['role'] === 'driver') {
    $user_id = $_SESSION['user_id'];
    $is_online = $_POST['is_online'] === 'true' ? 1 : 0;
    $lat = $_POST['lat'] ?? null;
    $lng = $_POST['lng'] ?? null;
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET is_online = ?, lat = ?, lng = ? WHERE id = ?");
        $stmt->execute([$is_online, $lat, $lng, $user_id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
}
