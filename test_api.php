<?php
session_start();
// Mocking a session if not interactive
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Assuming rider ID 1 exists
}
require_once 'includes/db.php';

try {
    $stmt = $pdo->prepare("SELECT u.id, u.name, u.lat, u.lng, v.model, v.type, v.plate_number 
                          FROM users u 
                          LEFT JOIN vehicles v ON u.id = v.driver_id 
                          WHERE u.role = 'driver' AND u.is_online = 1");
    $stmt->execute();
    $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'drivers' => $drivers]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
