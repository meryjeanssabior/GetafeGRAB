<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'rider') {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Dashboard | GetafeGRAB</title>
    <link rel="stylesheet" href="../assets/css/chat.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
</head>
<body class="dashboard-body">
    <div class="sidebar">
        <div class="logo">Getafe<span>GRAB</span></div>
        <div class="user-info">
            <p>Welcome, <strong><?php echo $_SESSION['name']; ?></strong></p>
        </div>
        
        <div class="booking-card glass-card">
            <h3>Book a Ride</h3>
            <div class="input-group">
                <label>Pickup Location</label>
                <div class="input-with-icon">
                    <input type="text" id="pickupInput" placeholder="Click on map or search">
                    <button id="locateMeBtn" title="Use current location"><i class="fas fa-location-arrow"></i></button>
                </div>
            </div>
            <div class="input-group">
                <label>Drop-off Location</label>
                <input type="text" id="dropoffInput" placeholder="Click on map or search">
            </div>

            <div class="vehicle-selection">
                <label>Select Service</label>
                <div class="vehicle-types">
                    <div class="v-type active" data-type="tricycle" data-rate="10.0">
                        <i class="fas fa-motorcycle"></i>
                        <span>Tricycle</span>
                    </div>
                    <div class="v-type" data-type="bike" data-rate="8.0">
                        <i class="fas fa-bicycle"></i>
                        <span>Bike</span>
                    </div>
                </div>
            </div>

            <div class="suggestions-section">
                <label><i class="fas fa-star"></i> Quick Destinations</label>
                <div class="suggestions-list">
                    <div class="suggestion-group">
                        <span>Transport</span>
                        <button class="suggest-btn" data-lat="10.1504" data-lng="124.1554" data-name="Getafe Port">Getafe Port</button>
                    </div>
                    <div class="suggestion-group">
                        <span>Town Center</span>
                        <button class="suggest-btn" data-lat="10.1458" data-lng="124.1504" data-name="Getafe Public Market">Public Market</button>
                        <button class="suggest-btn" data-lat="10.1428" data-lng="124.1524" data-name="Getafe Town Plaza">Town Plaza</button>
                        <button class="suggest-btn" data-lat="10.1462" data-lng="124.1512" data-name="Municipal Hall">Municipal Hall</button>
                    </div>
                    <div class="suggestion-group">
                        <span>Schools</span>
                        <button class="suggest-btn" data-lat="10.1448" data-lng="124.1514" data-name="Getafe Central School">Central School</button>
                        <button class="suggest-btn" data-lat="10.1452" data-lng="124.1508" data-name="St. Paul's Academy">St. Paul's Academy</button>
                    </div>
                </div>
            </div>
            
            <div id="fareEstimate" style="display:none;" class="fare-box">
                <p>Estimated Fare: <span id="fareValue">₱0.00</span></p>
            </div>

            <button id="bookRideBtn" class="btn-primary w-100" disabled>Find a Driver</button>
            <button id="cancelRideBtn" onclick="cancelBooking()" class="btn-outline w-100" style="display:none; margin-top: 10px; color: #ff4e50; border-color: #ff4e50; background: rgba(255,78,80,0.05); border-radius:12px; padding:0.8rem; font-weight:600; cursor:pointer;">Cancel Ride</button>
        </div>

        <nav class="side-nav">
            <a href="history.php" class="nav-item">
                <i class="fas fa-history"></i>
                <span>Trip History</span>
            </a>
            <a href="../logout.php" class="nav-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Driver Selection Modal -->
    <div id="driverSelectionModal" class="modal" style="display:none;">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h2><i class="fas fa-id-card"></i> Choose Your Driver</h2>
                <button class="close-modal" onclick="closeDriverModal()">&times;</button>
            </div>
            <div id="driverList" class="driver-list">
                <!-- Drivers will be loaded here -->
                <div class="loading-drivers">
                    <i class="fas fa-spinner fa-spin"></i> Finding nearby drivers...
                </div>
            </div>
        </div>
    </div>

    <div id="map"></div>

    <style>
        .dashboard-body {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 350px;
            background: #1a1a1a;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--glass-border);
            z-index: 1000;
            overflow-y: auto; /* Enable scrolling */
            scrollbar-width: thin;
            scrollbar-color: var(--primary) transparent;
        }

        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }

        .logo { font-size: 1.5rem; font-weight: 800; margin-bottom: 2rem; }
        .logo span { color: var(--primary); }

        .user-info { margin-bottom: 2rem; font-size: 0.9rem; }

        .nearby-status {
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            border-left: 4px solid #4cd137;
        }
        .status-header { display: flex; flex-direction: column; gap: 0.2rem; }
        .status-header i { color: #4cd137; font-size: 0.8rem; }
        .status-header span { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,0.5); }
        .eta-box { text-align: right; }
        .eta-box span { display: block; font-size: 1.2rem; font-weight: 800; color: var(--primary); }
        .eta-box label { font-size: 0.65rem; color: rgba(255,255,255,0.4); text-transform: uppercase; }

        .booking-card { padding: 1.5rem; margin-bottom: auto; }
        .booking-card h3 { margin-bottom: 1.5rem; font-size: 1.2rem; font-weight: 700; }

        .input-with-icon { position: relative; display: flex; align-items: center; }
        .input-with-icon input { padding-right: 3rem; }
        #locateMeBtn {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            font-size: 1.1rem;
            transition: 0.3s;
        }
        #locateMeBtn:hover { transform: scale(1.2); }

        .vehicle-selection { margin: 1.5rem 0; }
        .vehicle-selection label { font-size: 0.8rem; color: rgba(255,255,255,0.5); display: block; margin-bottom: 0.8rem; }
        .vehicle-types { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.8rem; }
        .v-type {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1rem 0.5rem;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .v-type i { font-size: 1.4rem; color: rgba(255,255,255,0.6); }
        .v-type span { font-size: 0.8rem; font-weight: 600; }
        .v-type.active {
            background: rgba(249, 212, 35, 0.12);
            border-color: var(--primary);
        }
        .v-type.active i { color: var(--primary); }
        .v-type:hover { background: rgba(255,255,255,0.08); transform: translateY(-2px); }

        .suggestions-section { margin: 2rem 0; }
        .suggestions-section label { font-size: 0.8rem; color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 1.2rem; }
        .suggestions-list { display: flex; flex-direction: column; gap: 1.2rem; }
        .suggestion-group { display: flex; flex-direction: column; gap: 0.6rem; }
        .suggestion-group span { font-size: 0.7rem; color: rgba(255,255,255,0.4); text-transform: uppercase; font-weight: 700; margin-left: 0.2rem; }
        .suggest-btn {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            color: var(--light);
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: 0.3s;
            text-align: left;
            display: flex;
            align-items: center;
        }
        .suggest-btn:before { content: '•'; color: var(--primary); margin-right: 12px; font-weight: 800; font-size: 1.2rem; }
        .suggest-btn:hover { 
            background: rgba(255,255,255,0.07); 
            border-color: var(--primary); 
            transform: translateX(8px);
        }

        .fare-box {
            background: linear-gradient(135deg, rgba(249, 212, 35, 0.1), rgba(255, 78, 80, 0.1));
            border: 1px solid rgba(249, 212, 35, 0.2);
            border-radius: 15px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        .fare-box p { font-size: 0.85rem; color: rgba(255,255,255,0.6); margin-bottom: 0.3rem; }
        .fare-box span { font-weight: 800; color: var(--primary); font-size: 1.5rem; display: block; }

        #map { flex: 1; height: 100%; position: relative; }

        .w-100 { width: 100%; }

        /* Modal Styling */
        .modal {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center; justify-content: center;
            z-index: 5000;
            animation: fadeIn 0.3s ease;
        }
        .modal-content {
            width: 90%; max-width: 500px;
            padding: 2.5rem;
            position: relative;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 2rem;
        }
        .modal-header h2 { margin: 0; font-size: 1.5rem; color: var(--primary); }
        .close-modal {
            background: none; border: none; color: #fff; font-size: 2rem; cursor: pointer;
            line-height: 1;
        }
        .driver-list {
            display: flex; flex-direction: column; gap: 1rem;
            max-height: 400px; overflow-y: auto; padding-right: 10px;
        }
        .driver-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1.2rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            transition: 0.3s;
            cursor: pointer;
        }
        .driver-item:hover {
            background: rgba(249, 212, 35, 0.1);
            border-color: var(--primary);
            transform: translateY(-2px);
        }
        .driver-info .d-name { display: block; font-weight: 700; font-size: 1.1rem; color: #fff; }
        .driver-info .d-meta { font-size: 0.85rem; color: rgba(255,255,255,0.6); }
        .driver-action .select-btn {
            background: var(--primary); color: #000;
            border: none; padding: 0.6rem 1.2rem;
            border-radius: 10px; font-weight: 700;
            font-size: 0.85rem;
        }
        .loading-drivers { text-align: center; padding: 3rem; color: rgba(255,255,255,0.5); }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>const currentUserId = <?= $_SESSION['user_id']; ?>;</script>
    <script src="../assets/js/chat.js"></script>
    <script src="../assets/js/map.js"></script>
</body>
</html>
