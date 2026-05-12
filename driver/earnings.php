<?php
session_start();
require_once __DIR__ . '/../../../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header('Location: ../login.php');
    exit();
}

$stmt = $pdo->prepare("SELECT b.*, u.name as rider_name FROM bookings b LEFT JOIN users u ON b.rider_id = u.id WHERE b.driver_id = ? AND b.status = 'completed' ORDER BY b.completed_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$history = $stmt->fetchAll();

$totalEarnings = 0;
foreach($history as $trip) $totalEarnings += $trip['fare'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Earnings | GetafeGRAB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body class="history-page">
    <div class="container">
        <header>
            <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
            <h1>My Earnings</h1>
        </header>

        <div class="earnings-summary glass-card">
            <h3>Total Earnings</h3>
            <p class="amount">₱<?php echo number_format($totalEarnings, 2); ?></p>
            <p class="subtitle">From <?php echo count($history); ?> completed trips</p>
        </div>

        <h2 class="section-title">Completed Trips</h2>
        <div class="history-list">
            <?php foreach ($history as $trip): ?>
            <div class="history-card glass-card">
                <div class="card-header">
                    <span class="status-badge completed">Completed</span>
                    <span class="date"><?php echo date('M d, Y h:i A', strtotime($trip['completed_at'])); ?></span>
                </div>
                <div class="trip-info">
                    <p><strong>Passenger:</strong> <?php echo $trip['rider_name']; ?></p>
                    <p><strong>From:</strong> <?php echo $trip['pickup_address']; ?></p>
                    <p><strong>To:</strong> <?php echo $trip['dropoff_address']; ?></p>
                </div>
                <div class="card-footer">
                    <span class="fare-label">Earned:</span>
                    <span class="fare">₱<?php echo number_format($trip['fare'], 2); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($history)): ?>
                <p class="empty-msg">No completed trips yet. Start driving to earn!</p>
            <?php endif; ?>
        </div>
    </div>

    <style>
        body { padding: 3rem 5%; background: #121212; }
        .container { max-width: 800px; margin: 0 auto; }
        header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 3rem; }
        .back-btn { color: var(--primary); text-decoration: none; font-weight: 600; }
        
        .earnings-summary { padding: 2.5rem; text-align: center; margin-bottom: 3rem; border-bottom: 4px solid var(--primary); }
        .earnings-summary h3 { font-size: 1rem; color: rgba(255,255,255,0.6); margin-bottom: 0.5rem; }
        .earnings-summary .amount { font-size: 3.5rem; font-weight: 800; color: var(--primary); }
        .earnings-summary .subtitle { font-size: 0.9rem; color: rgba(255,255,255,0.4); margin-top: 0.5rem; }

        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; }
        .history-list { display: flex; flex-direction: column; gap: 1.5rem; }
        .history-card { padding: 1.5rem; }
        .card-header { display: flex; justify-content: space-between; margin-bottom: 1rem; }
        .status-badge.completed { background: rgba(76, 209, 55, 0.2); color: #4cd137; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        
        .trip-info p { font-size: 0.9rem; margin-bottom: 0.5rem; color: rgba(255,255,255,0.8); }
        .card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem; }
        .fare-label { font-size: 0.8rem; color: rgba(255,255,255,0.5); }
        .fare { color: var(--primary); font-weight: 800; font-size: 1.4rem; }
        .empty-msg { text-align: center; color: rgba(255,255,255,0.4); padding: 5rem; }
    </style>
</body>
</html>
