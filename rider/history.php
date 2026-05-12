<?php
session_start();
require_once __DIR__ . '/../../../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'rider') {
    header('Location: ../login.php');
    exit();
}

$stmt = $pdo->prepare("SELECT b.*, u.name as driver_name, v.plate_number 
                      FROM bookings b 
                      LEFT JOIN users u ON b.driver_id = u.id 
                      LEFT JOIN vehicles v ON u.id = v.driver_id 
                      WHERE b.rider_id = ? 
                      ORDER BY b.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$history = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip History | GetafeGRAB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body class="history-page">
    <div class="container">
        <header>
            <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
            <h1>Trip History</h1>
        </header>

        <div class="history-list">
            <?php foreach ($history as $trip): ?>
            <div class="history-card glass-card">
                <div class="card-header">
                    <span class="status-badge <?php echo $trip['status']; ?>"><?php echo ucfirst($trip['status']); ?></span>
                    <span class="date"><?php echo date('M d, Y h:i A', strtotime($trip['created_at'])); ?></span>
                </div>
                <div class="trip-info">
                    <p><strong>From:</strong> <?php echo $trip['pickup_address']; ?></p>
                    <p><strong>To:</strong> <?php echo $trip['dropoff_address']; ?></p>
                </div>
                <div class="card-footer">
                    <div class="driver-details">
                        <span class="driver">Driver: <?php echo $trip['driver_name'] ?? 'N/A'; ?></span>
                        <?php if(isset($trip['plate_number'])): ?>
                            <span class="plate"> | ₱late: <?php echo $trip['plate_number']; ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="fare">₱<?php echo number_format($trip['fare'], 2); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($history)): ?>
                <p class="empty-msg">You haven't booked any rides yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <style>
        body { padding: 3rem 5%; background: #121212; }
        .container { max-width: 800px; margin: 0 auto; }
        header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 3rem; }
        .back-btn { color: var(--primary); text-decoration: none; font-weight: 600; }
        
        .history-list { display: flex; flex-direction: column; gap: 1.5rem; }
        .history-card { padding: 1.5rem; }
        .card-header { display: flex; justify-content: space-between; margin-bottom: 1rem; }
        .status-badge { padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-badge.completed { background: rgba(76, 209, 55, 0.2); color: #4cd137; }
        .status-badge.pending { background: rgba(249, 212, 35, 0.2); color: var(--primary); }
        .status-badge.cancelled { background: rgba(232, 65, 24, 0.2); color: #e84118; }
        
        .trip-info p { font-size: 0.9rem; margin-bottom: 0.5rem; color: rgba(255,255,255,0.8); }
        .card-footer { display: flex; justify-content: space-between; margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem; }
        .fare { color: var(--primary); font-weight: 800; font-size: 1.1rem; }
        .empty-msg { text-align: center; color: rgba(255,255,255,0.4); padding: 5rem; }
    </style>
</body>
</html>
