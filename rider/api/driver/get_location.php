<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    try {
        // Get the active booking for this rider with driver's current location
        $stmt = $pdo->prepare("SELECT b.id, b.status, b.driver_id, b.pickup_lat, b.pickup_lng, b.dropoff_lat, b.dropoff_lng,
                              u.lat as driver_lat, u.lng as driver_lng, u.name as driver_name, 
                              v.plate_number, v.type as vehicle_type
                              FROM bookings b 
                              JOIN users u ON b.driver_id = u.id 
                              LEFT JOIN vehicles v ON u.id = v.driver_id
                              WHERE b.rider_id = ? AND b.status IN ('accepted', 'in_progress') 
                              ORDER BY b.created_at DESC LIMIT 1");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();

        if ($result && $result['driver_lat'] && $result['driver_lng']) {
            echo json_encode([
                'success' => true,
                'driver' => [
                    'lat' => (float)$result['driver_lat'],
                    'lng' => (float)$result['driver_lng'],
                    'name' => $result['driver_name'],
                    'plate_number' => $result['plate_number'],
                    'vehicle_type' => $result['vehicle_type'] ?? 'tricycle'
                ],
                'booking' => [
                    'id' => (int)$result['id'],
                    'status' => $result['status'],
                    'pickup_lat' => (float)$result['pickup_lat'],
                    'pickup_lng' => (float)$result['pickup_lng'],
                    'dropoff_lat' => (float)$result['dropoff_lat'],
                    'dropoff_lng' => (float)$result['dropoff_lng']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No active booking or driver location unavailable']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
}
?>
