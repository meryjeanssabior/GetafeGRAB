<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $driver_id = $_SESSION['user_id'];
    $booking_id = $_POST['booking_id'];

    try {
        // Check if still pending
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND status = 'pending'");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();

        if ($booking) {
            $stmt = $pdo->prepare("UPDATE bookings SET driver_id = ?, status = 'accepted', accepted_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$driver_id, $booking_id]);
            
            // Fetch updated booking
            $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$booking_id]);
            $updatedBooking = $stmt->fetch();

            echo json_encode(['success' => true, 'booking' => $updatedBooking]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ride no longer available']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
