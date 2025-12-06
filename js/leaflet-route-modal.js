// js/leaflet-route-modal.js
// Handles showing a Leaflet map modal for driver route viewing

let leafletMap = null;
let leafletMarkerStart = null;
let leafletMarkerEnd = null;
let leafletRouteLayer = null;

function showRouteModal(driverLat, driverLng, destLat, destLng, destAddress) {
    // Create modal if not exists
    let modal = document.getElementById('leafletRouteModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'leafletRouteModal';
        modal.innerHTML = `
            <div class="modal-overlay" style="z-index:3000; position:fixed; inset:0; display:flex; align-items:center; justify-content:center;">
                <div class="modal-content w-full max-w-2xl p-0" style="max-width:600px; position:relative; z-index:3100;">
                    <div class="flex justify-between items-center p-4 border-b border-gray-200 bg-white rounded-t-xl">
                        <h3 class="text-lg font-bold text-gray-800">Route to Customer</h3>
                        <button id="closeLeafletRouteModal" class="text-gray-400 hover:text-gray-600 text-2xl"><i class="fas fa-times"></i></button>
                    </div>
                    <div id="leafletMapContainer" style="height:400px;width:100%;border-radius:0 0 12px 12px;overflow:hidden;"></div>
                    <div class="p-4 bg-gray-50 border-t border-gray-200 text-sm text-gray-700">
                        <span class="font-semibold">Destination:</span> <span id="leafletDestAddress"></span>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        document.getElementById('closeLeafletRouteModal').onclick = closeLeafletRouteModal;
    }
    // Always bring modal to front
    modal.style.zIndex = 4000;
    // If destAddress contains HTML, render as HTML, else as text
    const destElem = document.getElementById('leafletDestAddress');
    if (/<[a-z][\s\S]*>/i.test(destAddress)) {
        destElem.innerHTML = destAddress;
    } else {
        destElem.textContent = destAddress || '';
    }
    modal.style.display = 'flex';
    modal.classList.remove('hidden');

    // Remove and re-create map if needed (fixes blank map on repeated open)
    if (leafletMap) {
        leafletMap.remove();
        leafletMap = null;
    }
    // Always show a map, even if coordinates are invalid
    const validCoords = (lat, lng) => typeof lat === 'number' && typeof lng === 'number' && (lat !== 0 || lng !== 0);
    let mapDriverLat = driverLat, mapDriverLng = driverLng, mapDestLat = destLat, mapDestLng = destLng;
    // If coordinates are invalid, use Metro Manila as fallback
    if (!validCoords(mapDriverLat, mapDriverLng)) {
        mapDriverLat = 14.5995; mapDriverLng = 120.9842;
    }
    if (!validCoords(mapDestLat, mapDestLng)) {
        mapDestLat = 14.6091; mapDestLng = 121.0223;
    }
    setTimeout(() => {
        leafletMap = L.map('leafletMapContainer').setView([mapDestLat, mapDestLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(leafletMap);
        // Add markers
    leafletMarkerStart = L.marker([mapDriverLat, mapDriverLng], {icon: L.icon({iconUrl:'https://cdn-icons-png.flaticon.com/512/684/684908.png',iconSize:[32,32]})}).addTo(leafletMap).bindPopup('Your Location');
    leafletMarkerEnd = L.marker([mapDestLat, mapDestLng], {icon: L.icon({iconUrl:'https://cdn-icons-png.flaticon.com/512/684/684912.png',iconSize:[32,32]})}).addTo(leafletMap).bindPopup(`<b>Destination</b><br>${destAddress}`);
        // Only draw route if both coordinates are valid
        if (validCoords(driverLat, driverLng) && validCoords(destLat, destLng)) {
            leafletMarkerStart.openPopup();
            fetch(`https://router.project-osrm.org/route/v1/driving/${driverLng},${driverLat};${destLng},${destLat}?overview=full&geometries=geojson`)
                .then(res => res.json())
                .then(data => {
                    if (data.routes && data.routes.length > 0) {
                        const route = data.routes[0].geometry;
                        leafletRouteLayer = L.geoJSON(route, {color: '#3b82f6', weight: 5, opacity: 0.8}).addTo(leafletMap);
                        leafletMap.fitBounds(L.geoJSON(route).getBounds(), {padding:[30,30]});
                    }
                });
        }
    }, 100);
}

function closeLeafletRouteModal() {
    const modal = document.getElementById('leafletRouteModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }
    if (leafletRouteLayer && leafletMap) leafletMap.removeLayer(leafletRouteLayer);
}
