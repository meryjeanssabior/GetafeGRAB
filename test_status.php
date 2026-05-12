<?php
session_start();
$_SESSION['user_id'] = 1; // Rider
require_once 'includes/db.php';
$rider_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT b.*, u.name as driver_name, v.plate_number 
                      FROM bookings b 
                      LEFT JOIN users u ON b.driver_id = u.id 
                      LEFT JOIN vehicles v ON u.id = v.driver_id 
                      WHERE b.rider_id = ? AND b.status NOT IN ('completed', 'cancelled') 
                      ORDER BY b.created_at DESC LIMIT 1");
$stmt->execute([$rider_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode(['success' => true, 'booking' => $booking]);
