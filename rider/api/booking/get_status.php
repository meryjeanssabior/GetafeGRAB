<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    try {
        // Get the latest booking for this rider that isn't completed or cancelled
        $stmt = $pdo->prepare("SELECT b.*, u.name as driver_name, u.phone as driver_phone, v.model as vehicle_model, v.plate_number 
                              FROM bookings b 
                              LEFT JOIN users u ON b.driver_id = u.id 
                              LEFT JOIN vehicles v ON u.id = v.driver_id
                              WHERE b.rider_id = ? AND b.status NOT IN ('completed', 'cancelled') 
                              ORDER BY b.created_at DESC LIMIT 1");
        $stmt->execute([$user_id]);
        $booking = $stmt->fetch();

        echo json_encode(['success' => true, 'booking' => $booking]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
}
?>
