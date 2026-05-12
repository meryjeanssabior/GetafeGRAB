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
                } else if (b.status === 'in_progress') {
                    btn.innerText = "Trip in Progress...";
                    btn.style.background = "linear-gradient(90deg, #2d3436, #000)";
                } else if (b.status === 'completed') {
                    clearInterval(poll);
                    alert('Trip completed! Thank you for riding with GetafeGRAB.');
                    window.location.reload();
                } else if (b.status === 'pending') {
                    btn.innerText = "Waiting for Driver...";
                } else if (b.status === 'cancelled') {
                    clearInterval(poll);
                    window.location.reload();
                }
            } else {
                clearInterval(poll);
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
    }
}
checkActiveRide();
