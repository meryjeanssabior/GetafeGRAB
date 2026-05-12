<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT u.id, u.name, u.lat, u.lng, v.model, v.type, v.plate_number 
                              FROM users u 
                              LEFT JOIN vehicles v ON u.id = v.driver_id 
                              WHERE u.role = 'driver' AND u.is_online = 1");
        $stmt->execute();
        $drivers = $stmt->fetchAll();
        echo json_encode(['success' => true, 'drivers' => $drivers]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
}
