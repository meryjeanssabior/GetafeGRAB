let map = L.map('map').setView([10.1458, 124.1504], 15); // Getafe, Bohol
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
let currentRate = 10.0; // Default Tricycle rate (Pesos)

// Add Search Control
const geocoder = L.Control.geocoder({
    defaultMarkGeocode: false
})
.on('markgeocode', function(e) {
    const bbox = e.geocode.bbox;
    const poly = L.polygon([
        bbox.getSouthEast(),
        bbox.getNorthEast(),
        bbox.getNorthWest(),
        bbox.getSouthWest()
    ]);
    map.fitBounds(poly.getBounds());
    
    // Auto-set as pickup if none, else dropoff
    if (!pickupCoords) {
        setPickup(e.geocode.center, e.geocode.name);
    } else if (!dropoffCoords) {
        setDropoff(e.geocode.center, e.geocode.name);
    }
})
.addTo(map);

let pickupMarker, dropoffMarker;
let pickupCoords, dropoffCoords;

function setPickup(latlng, address) {
    pickupCoords = latlng;
    if (pickupMarker) map.removeLayer(pickupMarker);
    pickupMarker = L.marker(pickupCoords, {
        draggable: true,
        icon: L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/1216/1216733.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        })
    }).addTo(map).bindPopup("Pickup").openPopup();
    
    document.getElementById('pickupInput').value = address || `${pickupCoords.lat.toFixed(6)}, ${pickupCoords.lng.toFixed(6)}`;
    pickupMarker.on('dragend', updateFare);
}

function setDropoff(latlng, address) {
    dropoffCoords = latlng;
    if (dropoffMarker) map.removeLayer(dropoffMarker);
    dropoffMarker = L.marker(dropoffCoords, {
        draggable: true,
        icon: L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/1216/1216724.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        })
    }).addTo(map).bindPopup("Drop-off").openPopup();
    
    document.getElementById('dropoffInput').value = address || `${dropoffCoords.lat.toFixed(6)}, ${dropoffCoords.lng.toFixed(6)}`;
    dropoffMarker.on('dragend', updateFare);
    calculateFare();
}

map.on('click', function(e) {
    if (!pickupCoords) {
        setPickup(e.latlng);
    } else if (!dropoffCoords) {
        setDropoff(e.latlng);
    }
});

function updateFare() {
    if (pickupMarker) pickupCoords = pickupMarker.getLatLng();
    if (dropoffMarker) dropoffCoords = dropoffMarker.getLatLng();
    if (pickupCoords && dropoffCoords) calculateFare();
}

function calculateFare() {
    if (!pickupCoords || !dropoffCoords) return;
    let dist = map.distance(pickupCoords, dropoffCoords) / 1000;
    let baseFare = 5.00;
    let totalFare = baseFare + (dist * currentRate);
    
    document.getElementById('fareEstimate').style.display = 'block';
    document.getElementById('fareValue').innerText = `₱${totalFare.toFixed(2)}`;
    document.getElementById('bookRideBtn').disabled = false;
    document.getElementById('bookRideBtn').dataset.fare = totalFare.toFixed(2);
}

// Service type selection
document.querySelectorAll('.v-type').forEach(type => {
    type.addEventListener('click', function() {
        document.querySelectorAll('.v-type').forEach(v => v.classList.remove('active'));
        this.classList.add('active');
        currentRate = parseFloat(this.dataset.rate);
        calculateFare();
    });
});

// Locate Me functionality
document.getElementById('locateMeBtn').addEventListener('click', () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const latlng = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            map.setView(latlng, 16);
            setPickup(latlng, "Current Location");
        }, () => {
            alert("Could not get your location. Please check browser permissions.");
        });
    }
});

// Suggested Destinations logic
document.querySelectorAll('.suggest-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const latlng = {
            lat: parseFloat(this.dataset.lat),
            lng: parseFloat(this.dataset.lng)
        };
        const name = this.dataset.name;
        map.setView(latlng, 16);
        setDropoff(latlng, name);
    });
});

document.getElementById('bookRideBtn').addEventListener('click', () => {
    if (!pickupCoords || !dropoffCoords) {
        alert("Please select pickup and drop-off locations first.");
        return;
    }
    openDriverModal();
});

function openDriverModal() {
    const modal = document.getElementById('driverSelectionModal');
    const list = document.getElementById('driverList');
    modal.style.display = 'flex';
    list.innerHTML = '<div class="loading-drivers"><i class="fas fa-spinner fa-spin"></i> Finding nearby drivers in Getafe...</div>';

    fetch('/GetafeGRAB/rider/api/driver/get_online.php')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.drivers.length > 0) {
                list.innerHTML = '';
                data.drivers.forEach(d => {
                    const item = document.createElement('div');
                    item.className = 'driver-item';
                    item.onclick = () => confirmBookingWithDriver(d.id);
                    item.innerHTML = `
                        <div class="driver-info">
                            <span class="d-name">${d.name}</span>
                            <span class="d-meta">${d.type} • ${d.plate_number || 'No Plate'}</span>
                        </div>
                        <div class="driver-action">
                            <button class="select-btn">Select</button>
                        </div>
                    `;
                    list.appendChild(item);
                });
            } else {
                list.innerHTML = '<div class="loading-drivers">No drivers online right now. Try again in a moment!</div>';
            }
        });
}

function closeDriverModal() {
    document.getElementById('driverSelectionModal').style.display = 'none';
}

async function confirmBookingWithDriver(driverId) {
    closeDriverModal();
    const btn = document.getElementById('bookRideBtn');
    btn.innerText = "Requesting Ride...";
    btn.disabled = true;

    let fare = btn.dataset.fare;
    let data = new FormData();
    data.append('pickup_lat', pickupCoords.lat);
    data.append('pickup_lng', pickupCoords.lng);
    data.append('pickup_address', document.getElementById('pickupInput').value);
    data.append('dropoff_lat', dropoffCoords.lat);
    data.append('dropoff_lng', dropoffCoords.lng);
    data.append('dropoff_address', document.getElementById('dropoffInput').value);
    data.append('fare', fare);
    data.append('driver_id', driverId);

    try {
        const response = await fetch('/GetafeGRAB/rider/api/booking/create.php', {
            method: 'POST',
            body: data
        });
        const result = await response.json();
        if(result.success) {
            startStatusPolling();
        } else {
            alert(result.error || "Booking failed");
            btn.innerText = "Find a Driver";
            btn.disabled = false;
        }
    } catch (err) {
        console.error(err);
        btn.innerText = "Find a Driver";
        btn.disabled = false;
    }
}

function startStatusPolling() {
    const btn = document.getElementById('bookRideBtn');
    btn.innerText = "Finding Driver...";
    btn.disabled = true;
    btn.style.background = "linear-gradient(90deg, #f9d423, #ff4e50)";
    
    if (document.getElementById('cancelRideBtn')) {
        document.getElementById('cancelRideBtn').style.display = 'block';
    }

    const poll = setInterval(async () => {
        try {
            const res = await fetch('/GetafeGRAB/rider/api/booking/get_status.php');
            const data = await res.json();
            
            if (data.success && data.booking) {
                const b = data.booking;
                if (b.status === 'accepted') {
                    const driverInfo = `${b.driver_name} (${b.plate_number || 'Tricycle'})`;
                    btn.innerText = `Driver ${driverInfo} is coming!`;
                    btn.style.background = "linear-gradient(90deg, #4cd137, #44bd32)";
                    
                    if (typeof initChat === 'function' && !document.getElementById('chatWindow')) {
                        initChat(b.id, currentUserId);
                    }
                    // Start real-time driver tracking
                    startDriverTracking();
                } else if (b.status === 'in_progress') {
                    btn.innerText = "Trip in Progress...";
                    btn.style.background = "linear-gradient(90deg, #2d3436, #000)";
                    // Continue tracking during trip
                    startDriverTracking();
                } else if (b.status === 'completed') {
                    clearInterval(poll);
                    stopDriverTracking();
                    alert('Trip completed! Thank you for riding with GetafeGRAB.');
                    window.location.reload();
                } else if (b.status === 'pending') {
                    btn.innerText = "Waiting for Driver...";
                } else if (b.status === 'cancelled') {
                    clearInterval(poll);
                    stopDriverTracking();
                    window.location.reload();
                }
            } else {
                clearInterval(poll);
                stopDriverTracking();
                btn.innerText = "Find a Driver";
                btn.disabled = false;
                btn.style.background = "";
                if (document.getElementById('cancelRideBtn')) {
                    document.getElementById('cancelRideBtn').style.display = 'none';
                }
            }
        } catch (err) { console.error("Polling error:", err); }
    }, 3000);
}

async function cancelBooking() {
    if (!confirm("Are you sure you want to cancel this ride?")) return;
    try {
        const res = await fetch('/GetafeGRAB/rider/api/booking/get_status.php');
        const data = await res.json();
        if (data.success && data.booking) {
            const formData = new FormData();
            formData.append('booking_id', data.booking.id);
            formData.append('status', 'cancelled');
            await fetch('/GetafeGRAB/rider/api/booking/update_status.php', { method: 'POST', body: formData });
            window.location.reload();
        }
    } catch (err) { console.error(err); }
}

let nearbyDrivers = [];
const driverIcons = {
    tricycle: L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3063/3063822.png',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    }),
    bike: L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3448/3448636.png',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    })
};

async function initNearbyDrivers() {
    try {
        const response = await fetch('/GetafeGRAB/rider/api/driver/get_online.php');
        const result = await response.json();
        
        nearbyDrivers.forEach(d => map.removeLayer(d.marker));
        nearbyDrivers = [];
        
        if (result.success && result.drivers.length > 0) {
            result.drivers.forEach(d => {
                if (d.lat && d.lng) {
                    const marker = L.marker([d.lat, d.lng], { icon: driverIcons[d.type || 'tricycle'] })
                        .addTo(map)
                        .bindPopup(`
                            <div class="driver-popup" style="color:#000; padding:10px;">
                                <strong>${d.name}</strong><br>
                                <span style="text-transform:capitalize;">${d.type}</span> | <span>${d.plate_number || 'No Plate'}</span><br>
                                <button class="btn-primary" style="margin-top:10px; padding:5px 10px; width:100%; font-size:0.8rem;" onclick="selectDriverForBooking(${d.id}, '${d.name}')">Request this Driver</button>
                            </div>
                        `);
                    nearbyDrivers.push({ marker, type: d.type, lat: d.lat, lng: d.lng, id: d.id, name: d.name });
                }
            });
        }
        updateNearestETA();
    } catch (err) { console.error(err); }
}

setInterval(initNearbyDrivers, 15000);

let selectedDriverId = null;
function selectDriverForBooking(driverId, driverName) {
    selectedDriverId = driverId;
    const btn = document.getElementById('bookRideBtn');
    btn.innerText = `Request ${driverName}`;
    btn.style.background = "linear-gradient(90deg, #4cd137, #44bd32)";
    alert(`Driver ${driverName} selected! Click 'Request' below.`);
}

function updateNearestETA() {
    const targetPoint = pickupCoords || map.getCenter();
    let minDist = Infinity;
    
    nearbyDrivers.forEach(d => {
        const dist = map.distance(targetPoint, [d.lat, d.lng]);
        if (dist < minDist) minDist = dist;
    });
    
    const mins = Math.max(2, Math.round(minDist / 200)); 
    const etaEl = document.getElementById('nearestETA');
    if (etaEl) etaEl.innerText = `${mins} mins`;
    
    setTimeout(updateNearestETA, 10000);
}

initNearbyDrivers();

// Check if there's an active ride on load
async function checkActiveRide() {
    const res = await fetch('/GetafeGRAB/rider/api/booking/get_status.php');
    const data = await res.json();
    if (data.success && data.booking) {
        startStatusPolling();
        const b = data.booking;
        if ((b.status === 'accepted' || b.status === 'in_progress') && typeof initChat === 'function') {
            initChat(b.id, currentUserId);
        }
        // Start real-time tracking if ride is active
        if (b.status === 'accepted' || b.status === 'in_progress') {
            startDriverTracking();
        }
    }
}
checkActiveRide();

// ===== Real-Time Driver Tracking on Rider Map =====
let driverTrackingMarker = null;
let driverRouteLine = null;
let driverTrackingInterval = null;
let trackingInfoPanel = null;
let driverPulseCircle = null;
let lastDriverLatLng = null;
let animationFrame = null;

const driverTrackingIcon = L.divIcon({
    className: 'driver-tracking-icon',
    html: `
        <div class="driver-marker-container">
            <div class="driver-marker-pulse"></div>
            <div class="driver-marker-dot">
                <i class="fas fa-motorcycle"></i>
            </div>
        </div>
    `,
    iconSize: [50, 50],
    iconAnchor: [25, 25]
});

// Inject tracking CSS
(function injectTrackingStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .driver-tracking-icon {
            background: none !important;
            border: none !important;
        }
        .driver-marker-container {
            position: relative;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .driver-marker-pulse {
            position: absolute;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(249, 212, 35, 0.25);
            animation: driverPulse 2s ease-out infinite;
        }
        .driver-marker-dot {
            position: relative;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f9d423, #ff4e50);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(249, 212, 35, 0.5), 0 0 30px rgba(249, 212, 35, 0.2);
            z-index: 2;
        }
        .driver-marker-dot i {
            color: #1a1a1a;
            font-size: 16px;
        }
        @keyframes driverPulse {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(2.2); opacity: 0; }
        }

        .tracking-info-panel {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2000;
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(249, 212, 35, 0.2);
            border-radius: 20px;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5), 0 0 20px rgba(249, 212, 35, 0.08);
            animation: slideUpFade 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            min-width: 380px;
        }
        @keyframes slideUpFade {
            from { transform: translateX(-50%) translateY(30px); opacity: 0; }
            to { transform: translateX(-50%) translateY(0); opacity: 1; }
        }
        .tracking-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f9d423, #ff4e50);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .tracking-avatar i {
            color: #1a1a1a;
            font-size: 18px;
        }
        .tracking-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }
        .tracking-driver-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: #fff;
        }
        .tracking-status-label {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .tracking-stats {
            display: flex;
            gap: 16px;
            flex-shrink: 0;
        }
        .tracking-stat {
            text-align: center;
        }
        .tracking-stat-value {
            font-size: 1.1rem;
            font-weight: 800;
            color: #f9d423;
            display: block;
        }
        .tracking-stat-label {
            font-size: 0.65rem;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .tracking-live-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: rgba(76, 209, 55, 0.15);
            border: 1px solid rgba(76, 209, 55, 0.3);
            border-radius: 20px;
            flex-shrink: 0;
        }
        .tracking-live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4cd137;
            animation: liveBlink 1.5s ease-in-out infinite;
        }
        @keyframes liveBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        .tracking-live-text {
            font-size: 0.7rem;
            font-weight: 700;
            color: #4cd137;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    `;
    document.head.appendChild(style);
})();

function createTrackingInfoPanel() {
    if (trackingInfoPanel) trackingInfoPanel.remove();
    
    trackingInfoPanel = document.createElement('div');
    trackingInfoPanel.className = 'tracking-info-panel';
    trackingInfoPanel.id = 'trackingInfoPanel';
    trackingInfoPanel.innerHTML = `
        <div class="tracking-avatar"><i class="fas fa-motorcycle"></i></div>
        <div class="tracking-details">
            <span class="tracking-driver-name" id="trackingDriverName">Locating driver...</span>
            <span class="tracking-status-label" id="trackingStatusLabel">Connecting</span>
        </div>
        <div class="tracking-stats">
            <div class="tracking-stat">
                <span class="tracking-stat-value" id="trackingETA">--</span>
                <span class="tracking-stat-label">ETA</span>
            </div>
            <div class="tracking-stat">
                <span class="tracking-stat-value" id="trackingDist">--</span>
                <span class="tracking-stat-label">Away</span>
            </div>
        </div>
        <div class="tracking-live-badge">
            <div class="tracking-live-dot"></div>
            <span class="tracking-live-text">Live</span>
        </div>
    `;
    document.body.appendChild(trackingInfoPanel);
}

function smoothMoveMarker(marker, targetLatLng, duration) {
    if (animationFrame) cancelAnimationFrame(animationFrame);
    
    const startLatLng = marker.getLatLng();
    const startTime = performance.now();
    
    function animate(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Ease-out cubic
        const eased = 1 - Math.pow(1 - progress, 3);
        
        const lat = startLatLng.lat + (targetLatLng.lat - startLatLng.lat) * eased;
        const lng = startLatLng.lng + (targetLatLng.lng - startLatLng.lng) * eased;
        
        marker.setLatLng([lat, lng]);
        
        if (progress < 1) {
            animationFrame = requestAnimationFrame(animate);
        }
    }
    
    animationFrame = requestAnimationFrame(animate);
}

function updateTrackingRoute(driverLatLng, destinationLatLng, status) {
    if (driverRouteLine) map.removeLayer(driverRouteLine);
    
    driverRouteLine = L.polyline([driverLatLng, destinationLatLng], {
        color: status === 'accepted' ? '#4cd137' : '#f9d423',
        weight: 4,
        opacity: 0.8,
        dashArray: '12, 8',
        lineCap: 'round',
        lineJoin: 'round'
    }).addTo(map);
}

function updateTrackingPanel(driverData, bookingData) {
    const driverLatLng = L.latLng(driverData.lat, driverData.lng);
    
    // Determine destination based on status
    let destLatLng;
    let statusLabel;
    if (bookingData.status === 'accepted') {
        destLatLng = L.latLng(bookingData.pickup_lat, bookingData.pickup_lng);
        statusLabel = 'Heading to pickup';
    } else {
        destLatLng = L.latLng(bookingData.dropoff_lat, bookingData.dropoff_lng);
        statusLabel = 'Trip in progress';
    }
    
    const distMeters = map.distance(driverLatLng, destLatLng);
    const distKm = distMeters / 1000;
    const etaMins = Math.max(1, Math.round(distMeters / 200)); // ~12km/h avg speed for tricycle
    
    const nameEl = document.getElementById('trackingDriverName');
    const statusEl = document.getElementById('trackingStatusLabel');
    const etaEl = document.getElementById('trackingETA');
    const distEl = document.getElementById('trackingDist');
    
    if (nameEl) nameEl.textContent = `${driverData.name} • ${driverData.plate_number || 'No Plate'}`;
    if (statusEl) statusEl.textContent = statusLabel;
    if (etaEl) etaEl.textContent = `${etaMins}m`;
    if (distEl) distEl.textContent = distKm < 1 ? `${Math.round(distMeters)}m` : `${distKm.toFixed(1)}km`;
    
    // Update route line
    updateTrackingRoute(driverLatLng, destLatLng, bookingData.status);
}

function startDriverTracking() {
    if (driverTrackingInterval) return; // Already tracking
    
    createTrackingInfoPanel();
    let firstUpdate = true;
    
    async function pollDriverLocation() {
        try {
            const res = await fetch('/GetafeGRAB/rider/api/driver/get_location.php');
            const data = await res.json();
            
            if (data.success && data.driver) {
                const driverLatLng = L.latLng(data.driver.lat, data.driver.lng);
                
                if (!driverTrackingMarker) {
                    // Create tracking marker on first position
                    driverTrackingMarker = L.marker(driverLatLng, {
                        icon: driverTrackingIcon,
                        zIndexOffset: 1000
                    }).addTo(map);
                    
                    if (firstUpdate) {
                        // Fit map to show both driver and pickup/dropoff
                        const bounds = L.latLngBounds([driverLatLng]);
                        if (pickupCoords) bounds.extend(pickupCoords);
                        if (dropoffCoords) bounds.extend(dropoffCoords);
                        map.fitBounds(bounds, { padding: [60, 60] });
                        firstUpdate = false;
                    }
                } else {
                    // Smooth animate to new position
                    smoothMoveMarker(driverTrackingMarker, driverLatLng, 2500);
                }
                
                lastDriverLatLng = driverLatLng;
                updateTrackingPanel(data.driver, data.booking);
            }
        } catch (err) {
            console.error('Driver tracking error:', err);
        }
    }
    
    // First poll immediately
    pollDriverLocation();
    // Then every 3 seconds
    driverTrackingInterval = setInterval(pollDriverLocation, 3000);
}

function stopDriverTracking() {
    if (driverTrackingInterval) {
        clearInterval(driverTrackingInterval);
        driverTrackingInterval = null;
    }
    if (animationFrame) {
        cancelAnimationFrame(animationFrame);
        animationFrame = null;
    }
    if (driverTrackingMarker) {
        map.removeLayer(driverTrackingMarker);
        driverTrackingMarker = null;
    }
    if (driverRouteLine) {
        map.removeLayer(driverRouteLine);
        driverRouteLine = null;
    }
    if (trackingInfoPanel) {
        trackingInfoPanel.remove();
        trackingInfoPanel = null;
    }
    lastDriverLatLng = null;
}
