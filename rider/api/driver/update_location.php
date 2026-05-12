<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && $_SESSION['role'] === 'driver') {
    $driver_id = $_SESSION['user_id'];
    $lat = $_POST['lat'] ?? null;
    $lng = $_POST['lng'] ?? null;
    
    if ($lat && $lng) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET lat = ?, lng = ?, last_seen = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$lat, $lng, $driver_id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing lat/lng']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
}
?>
