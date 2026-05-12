<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard | GetafeGRAB</title>
    <link rel="stylesheet" href="../assets/css/chat.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <div class="sidebar">
        <div class="logo">Getafe<span>GRAB</span></div>
        <div class="user-info">
            <p>Welcome, Driver <strong><?php echo $_SESSION['name']; ?></strong></p>
        </div>
        
        <nav class="side-nav">
            <a href="dashboard.php" class="nav-item active">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="earnings.php" class="nav-item">
                <i class="fas fa-wallet"></i>
                <span>Earnings</span>
            </a>
            <a href="../logout.php" class="nav-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="status-toggle">
                <span id="statusText">Go Online</span>
                <label class="switch">
                    <input type="checkbox" id="onlineToggle">
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="stats-row">
                <div class="stat-card">
                    <label>Earnings Today</label>
                    <span id="statEarnings">₱0.00</span>
                </div>
                <div class="stat-card">
                    <label>Trips Today</label>
                    <span id="statTrips">0</span>
                </div>
                <div class="stat-card">
                    <label>Rating</label>
                    <span id="statRating"><i class="fas fa-star"></i> 4.8</span>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="feed-section">
                <h2>Available Jobs</h2>
                <div id="rideRequestsList" class="requests-feed">
                    <div class="loading-spinner">Waiting for requests...</div>
                </div>
            </div>
            <div class="map-section">
                <div id="driverMap" class="glass-card"></div>
            </div>
        </div>

        <div id="activeRide" class="active-ride-modal" style="display:none;">
            <div class="active-ride-content glass-card">
                <h2>Active Trip</h2>
                <div class="route-info">
                    <div class="point"><i class="fas fa-map-marker-alt" style="color:#00ff00"></i> <span id="activePickup"></span></div>
                    <div class="line"></div>
                    <div class="point"><i class="fas fa-map-marker-alt" style="color:#ff0000"></i> <span id="activeDropoff"></span></div>
                </div>
                <div class="fare-info">Fare: <span id="activeFare"></span></div>
                <div id="statusActions">
                    <button id="startTripBtn" class="btn-primary">Start Trip</button>
                    <button id="completeTripBtn" class="btn-primary" style="display:none;">Complete Trip</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dashboard-body { display: flex; height: 100vh; overflow: hidden; background: #121212; color: #fff; }
        .sidebar { 
            width: 300px; background: #1a1a1a; padding: 2rem; 
            border-right: 1px solid var(--glass-border); 
            display: flex; flex-direction: column; 
            z-index: 1000;
            overflow-y: auto;
        }
        
        .side-nav { margin-top: 2rem; display: flex; flex-direction: column; gap: 0.5rem; }
        .nav-item {
            display: flex; align-items: center; gap: 1rem;
            color: rgba(255,255,255,0.6); text-decoration: none;
            padding: 1rem; border-radius: 12px; transition: 0.3s;
            background: rgba(255,255,255,0.02);
        }
        .nav-item:hover, .nav-item.active { 
            color: var(--primary); background: rgba(249, 212, 35, 0.05); 
            border: 1px solid rgba(249, 212, 35, 0.1);
        }
        .nav-item.logout:hover { color: #ff4e50; background: rgba(255, 78, 80, 0.05); }

        .main-content { flex: 1; padding: 2rem; display: flex; flex-direction: column; gap: 2rem; overflow-y: auto; }

        .top-bar { display: flex; align-items: center; justify-content: space-between; gap: 2rem; }
        .stats-row { display: flex; gap: 1.5rem; flex: 1; }
        .stat-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 1.5rem; border-radius: 20px; flex: 1;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .stat-card label { display: block; font-size: 0.75rem; text-transform: uppercase; color: rgba(255,255,255,0.4); margin-bottom: 0.5rem; }
        .stat-card span { font-size: 1.5rem; font-weight: 800; color: var(--primary); }

        .status-toggle { display: flex; align-items: center; gap: 1rem; background: var(--glass); padding: 0.8rem 1.5rem; border-radius: 15px; border: 1px solid var(--glass-border); }
        .status-toggle span { font-weight: 700; color: #ff4e50; }
        .status-toggle.online span { color: #4cd137; }

        .dashboard-grid { display: flex; gap: 2rem; flex: 1; min-height: 0; }
        .feed-section { flex: 1; display: flex; flex-direction: column; gap: 1rem; }
        .requests-feed { display: flex; flex-direction: column; gap: 1.2rem; overflow-y: auto; padding-right: 0.5rem; }
        .map-section { flex: 1; }
        #driverMap { height: 100%; border-radius: 25px; border: 1px solid var(--glass-border); overflow: hidden; min-height: 400px; }

        .request-card {
            padding: 1.5rem; border: 1px solid var(--glass-border); border-radius: 20px;
            background: var(--glass); border-left: 4px solid var(--primary);
            transition: 0.3s;
        }
        .request-card:hover { transform: translateX(8px); background: rgba(255,255,255,0.08); }
        .request-card h3 { font-size: 1rem; margin-bottom: 1rem; color: var(--primary); }
        .request-card .route { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.2rem; }
        .request-card .route div { font-size: 0.85rem; color: rgba(255,255,255,0.7); }

        .active-ride-modal {
            position: fixed; inset: 0; background: rgba(0,0,0,0.8);
            backdrop-filter: blur(10px); z-index: 2000;
            display: flex; align-items: center; justify-content: center;
        }
        .active-ride-content { width: 100%; max-width: 500px; padding: 2.5rem; text-align: center; }
        .route-info { margin: 2rem 0; text-align: left; }
        .point { display: flex; align-items: center; gap: 1rem; font-weight: 600; margin: 1rem 0; }
        .line { width: 2px; height: 30px; background: rgba(255,255,255,0.1); margin-left: 7px; }
        .fare-info { font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-bottom: 2rem; }

        /* Switch styles */
        .switch { position: relative; display: inline-block; width: 60px; height: 34px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ff4e50; transition: .4s; }
        .slider:before { position: absolute; content: ""; height: 26px; width: 26px; left: 4px; bottom: 4px; background-color: white; transition: .4s; }
        input:checked + .slider { background-color: #4cd137; }
        input:checked + .slider:before { transform: translateX(26px); }
        .slider.round { border-radius: 34px; }
        .slider.round:before { border-radius: 50%; }
    </style>
    <script>const currentUserId = <?= $_SESSION['user_id']; ?>;</script>
    <script src="../assets/js/chat.js"></script>
    <script src="../assets/js/driver.js"></script>
</body>
</html>
