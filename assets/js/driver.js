let currentBooking = null;
let map, driverMarker;
let jobMarkers = [];
let isOnline = false;
let locationWatchId = null;
let locationUpdateTimer = null;

// Initialize Map
function initMap() {
    map = L.map('driverMap').setView([10.1458, 124.1504], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    driverMarker = L.marker([10.1458, 124.1504], {
        icon: L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/3063/3063822.png',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        })
    }).addTo(map).bindPopup("You are here");
}

async function fetchStats() {
    const response = await fetch('/GetafeGRAB/rider/api/driver/stats.php');
    const result = await response.json();
    if (result.success) {
        document.getElementById('statEarnings').innerText = `₱${result.stats.total_earnings}`;
        document.getElementById('statTrips').innerText = result.stats.trips_today;
        document.getElementById('statRating').innerHTML = `<i class="fas fa-star"></i> ${result.stats.rating}`;
    }
}

async function fetchRequests() {
    if (!isOnline) return;
    
    const response = await fetch('/GetafeGRAB/rider/api/booking/get_available.php');
    const result = await response.json();
    
    if (result.success) {
        const list = document.getElementById('rideRequestsList');
        list.innerHTML = '';
        jobMarkers.forEach(m => map.removeLayer(m));
        jobMarkers = [];
        
        if (result.bookings.length === 0) {
            list.innerHTML = '<div class="loading-spinner">No pending rides nearby.</div>';
            return;
        }

        result.bookings.forEach(booking => {
            // Add Marker
            const marker = L.marker([booking.pickup_lat, booking.pickup_lng]).addTo(map)
                .bindPopup(`<b>New Job</b><br>To: ${booking.dropoff_address}<br>₱${booking.fare}`);
            jobMarkers.push(marker);

            const card = document.createElement('div');
            card.className = 'request-card';
            card.innerHTML = `
                <h3>New ride request</h3>
                <div class="route">
                    <div><i class="fas fa-circle" style="color:#4cd137; font-size:10px"></i> From: ${booking.pickup_address}</div>
                    <div><i class="fas fa-circle" style="color:#ff4e50; font-size:10px"></i> To: ${booking.dropoff_address}</div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-weight:800; font-size:1.2rem; color:var(--primary);">₱${booking.fare}</span>
                    <button class="btn-primary" onclick="acceptRide(${booking.id})" style="padding:0.6rem 1.2rem; font-size:0.8rem;">Accept Ride</button>
                </div>
            `;
            list.appendChild(card);
        });
    }
}

async function acceptRide(bookingId) {
    const data = new FormData();
    data.append('booking_id', bookingId);
    
    const response = await fetch('/GetafeGRAB/rider/api/booking/accept.php', {
        method: 'POST',
        body: data
    });
    const result = await response.json();
    if (result.success) {
        currentBooking = result.booking;
        showActiveRide(currentBooking);
        alert('Ride accepted! Proceed to pickup.');
    } else {
        alert(result.error || 'Could not accept ride');
    }
}

function showActiveRide(booking) {
    document.getElementById('activeRide').style.display = 'flex';
    document.getElementById('activePickup').innerText = booking.pickup_address;
    document.getElementById('activeDropoff').innerText = booking.dropoff_address;
    document.getElementById('activeFare').innerText = `₱${booking.fare}`;
    
    // Initialize Chat
    if (typeof initChat === 'function' && !document.getElementById('chatWindow')) {
        initChat(booking.id, currentUserId);
    }
}

// Toggle Online/Offline
document.getElementById('onlineToggle').addEventListener('change', async function() {
    isOnline = this.checked;
    const statusBox = document.querySelector('.status-toggle');
    const statusText = document.getElementById('statusText');
    
    if (isOnline) {
        statusBox.classList.add('online');
        statusText.innerText = 'Online';
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                updateServerStatus(true, pos.coords.latitude, pos.coords.longitude);
            }, () => {
                updateServerStatus(true, 10.1458, 124.1504);
            });
        } else {
            updateServerStatus(true, 10.1458, 124.1504);
        }
        startLocationTracking();
        fetchRequests();
    } else {
        statusBox.classList.remove('online');
        statusText.innerText = 'Go Online';
        stopLocationTracking();
        updateServerStatus(false);
        document.getElementById('rideRequestsList').innerHTML = '<div class="loading-spinner">You are offline.</div>';
        jobMarkers.forEach(m => map.removeLayer(m));
    }
});

async function updateServerStatus(online, lat = null, lng = null) {
    const data = new FormData();
    data.append('is_online', online);
    if (lat) data.append('lat', lat);
    if (lng) data.append('lng', lng);
    await fetch('/GetafeGRAB/rider/api/driver/update_status.php', { method: 'POST', body: data });
}

document.getElementById('startTripBtn').addEventListener('click', () => updateStatus('in_progress'));
document.getElementById('completeTripBtn').addEventListener('click', () => updateStatus('completed'));

async function updateStatus(status) {
    const data = new FormData();
    data.append('booking_id', currentBooking.id);
    data.append('status', status);
    
    const response = await fetch('/GetafeGRAB/rider/api/booking/update_status.php', { method: 'POST', body: data });
    const result = await response.json();
    
    if (result.success) {
        if (status === 'in_progress') {
            document.getElementById('startTripBtn').style.display = 'none';
            document.getElementById('completeTripBtn').style.display = 'block';
        } else if (status === 'completed') {
            if (typeof chatPollInterval !== 'undefined') clearInterval(chatPollInterval);
            const chatWin = document.getElementById('chatWindow');
            const chatBtn = document.getElementById('chatToggleBtn');
            if (chatWin) chatWin.remove();
            if (chatBtn) chatBtn.remove();
            window.location.reload();
        }
    }
}

initMap();
fetchStats();
setInterval(fetchRequests, 5000);
setInterval(fetchStats, 30000);

// ===== Real-Time GPS Location Tracking =====
function startLocationTracking() {
    if (!navigator.geolocation) {
        console.warn('Geolocation not supported');
        return;
    }

    // Watch position for smooth local marker updates
    locationWatchId = navigator.geolocation.watchPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            if (driverMarker) {
                driverMarker.setLatLng([lat, lng]);
            }
        },
        (err) => console.warn('GPS watch error:', err),
        { enableHighAccuracy: true, maximumAge: 2000, timeout: 10000 }
    );

    // Push location to server every 3 seconds
    locationUpdateTimer = setInterval(() => {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const data = new FormData();
                data.append('lat', pos.coords.latitude);
                data.append('lng', pos.coords.longitude);
                fetch('/GetafeGRAB/rider/api/driver/update_location.php', {
                    method: 'POST',
                    body: data
                }).catch(err => console.error('Location push error:', err));
            },
            (err) => console.warn('GPS error:', err),
            { enableHighAccuracy: true, maximumAge: 3000, timeout: 5000 }
        );
    }, 3000);
}

function stopLocationTracking() {
    if (locationWatchId !== null) {
        navigator.geolocation.clearWatch(locationWatchId);
        locationWatchId = null;
    }
    if (locationUpdateTimer !== null) {
        clearInterval(locationUpdateTimer);
        locationUpdateTimer = null;
    }
}
