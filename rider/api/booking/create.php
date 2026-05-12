<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $rider_id = $_SESSION['user_id'];
    $pickup_lat = $_POST['pickup_lat'];
    $pickup_lng = $_POST['pickup_lng'];
    $pickup_address = $_POST['pickup_address'];
    $dropoff_lat = $_POST['dropoff_lat'];
    $dropoff_lng = $_POST['dropoff_lng'];
    $dropoff_address = $_POST['dropoff_address'];
    $fare = $_POST['fare'];
    $driver_id = $_POST['driver_id'] ?? null;

    try {
        $stmt = $pdo->prepare("INSERT INTO bookings (rider_id, driver_id, pickup_lat, pickup_lng, pickup_address, dropoff_lat, dropoff_lng, dropoff_address, fare, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$rider_id, $driver_id, $pickup_lat, $pickup_lng, $pickup_address, $dropoff_lat, $dropoff_lng, $dropoff_address, $fare]);
        
        echo json_encode(['success' => true, 'booking_id' => $pdo->lastInsertId()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized or invalid request']);
}
?>
