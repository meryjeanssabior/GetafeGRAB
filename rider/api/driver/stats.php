<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$driverId = $_SESSION['user_id'];

try {
    // Get Total Earnings
    $stmt = $pdo->prepare("SELECT SUM(fare) as total FROM bookings WHERE driver_id = ? AND status = 'completed'");
    $stmt->execute([$driverId]);
    $earnings = $stmt->fetch();
    $totalEarnings = $earnings['total'] ?? 0;

    // Get Today's Trips
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM bookings WHERE driver_id = ? AND status = 'completed' AND DATE(created_at) = CURDATE()");
    $stmt->execute([$driverId]);
    $tripsToday = $stmt->fetch()['count'] ?? 0;

    // Simulated Rating (would normally use a ratings table)
    $rating = 4.8; 

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_earnings' => number_format($totalEarnings, 2),
            'trips_today' => $tripsToday,
            'rating' => $rating
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
