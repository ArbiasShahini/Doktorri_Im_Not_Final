<?php
session_start();
require 'db.php';

function distanceKm($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earthRadius * $c;
}

$userLat = isset($_GET['lat']) && $_GET['lat'] !== '' ? floatval($_GET['lat']) : null;
$userLon = isset($_GET['lon']) && $_GET['lon'] !== '' ? floatval($_GET['lon']) : null;
$search = $_GET['q'] ?? '';

$params = [];
$sql = "SELECT id, name, city, address, latitude, longitude FROM pharmacy";
if ($search !== '') {
    $sql .= " WHERE name LIKE ? OR city LIKE ?";
    $like = "%".$search."%";
    $params[] = $like;
    $params[] = $like;
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param("ss", $params[0], $params[1]);
}
$stmt->execute();
$res = $stmt->get_result();

$pharmacies = [];
while ($row = $res->fetch_assoc()) {
    if ($userLat && $userLon && $row['latitude'] && $row['longitude']) {
        $row['distance'] = distanceKm($userLat, $userLon, $row['latitude'], $row['longitude']);
    } else {
        $row['distance'] = null;
    }
    $pharmacies[] = $row;
}

if ($userLat && $userLon) {
    usort($pharmacies, function($a, $b) {
        if ($a['distance'] === null && $b['distance'] === null) return 0;
        if ($a['distance'] === null) return 1;
        if ($b['distance'] === null) return -1;
        return $a['distance'] <=> $b['distance'];
    });
}

$pharmacyJson = json_encode($pharmacies);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacy | MyDoctor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        body {
            background: #f8fafc;
        }
        .page {
            max-width: 1200px;
            margin: 40px auto 60px auto;
            padding: 0 24px;
        }
        .page-header {
            margin-bottom: 24px;
        }
        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .page-subtitle {
            font-size: 14px;
            color: #64748b;
        }
        .pharmacy-controls {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .search-input {
            flex: 1;
            min-width: 260px;
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            font-size: 14px;
        }
        .btn-primary {
            padding: 10px 18px;
            border-radius: 999px;
            border: none;
            background: #2563eb;
            color: #ffffff;
            font-size: 14px;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        #map {
            width: 100%;
            height: 360px;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }
        .pharmacy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
        }
        .pharmacy-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 16px 18px;
            box-shadow: 0 12px 25px rgba(15,23,42,0.08);
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .pharmacy-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 15px;
        }
        .pharmacy-city {
            font-size: 13px;
            color: #64748b;
        }
        .pharmacy-address {
            font-size: 13px;
            color: #475569;
        }
        .pharmacy-distance {
            margin-top: 6px;
            font-size: 13px;
            color: #16a34a;
            font-weight: 500;
        }
        .badge-user-location {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 12px;
            margin-left: auto;
        }
        .no-results {
            margin-top: 20px;
            font-size: 14px;
            color: #6b7280;
        }
        @media (max-width: 640px) {
            .page {
                margin-top: 24px;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page">
    <div class="page-header">
        <div class="page-title">Pharmacy</div>
        <div class="page-subtitle">Find the nearest pharmacies based on your location.</div>
    </div>

    <form method="get" class="pharmacy-controls">
        <input type="text" name="q" class="search-input"
               placeholder="Search pharmacy or city..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="button" class="btn-primary" onclick="detectLocation()">Use my location</button>
        <?php if ($userLat && $userLon): ?>
            <span class="badge-user-location">Location detected</span>
        <?php endif; ?>
        <?php if ($userLat && $userLon): ?>
            <input type="hidden" name="lat" value="<?= htmlspecialchars($userLat) ?>">
            <input type="hidden" name="lon" value="<?= htmlspecialchars($userLon) ?>">
        <?php endif; ?>
        <button type="submit" class="btn-primary">Search</button>
    </form>

    <div id="map"></div>

    <?php if (count($pharmacies) === 0): ?>
        <div class="no-results">No pharmacies found.</div>
    <?php else: ?>
        <div class="pharmacy-grid">
            <?php foreach ($pharmacies as $p): ?>
                <div class="pharmacy-card">
                    <div class="pharmacy-name"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="pharmacy-city"><?= htmlspecialchars($p['city']) ?></div>
                    <?php if (!empty($p['address'])): ?>
                        <div class="pharmacy-address"><?= htmlspecialchars($p['address']) ?></div>
                    <?php endif; ?>
                    <?php if ($p['distance'] !== null): ?>
                        <div class="pharmacy-distance">
                            Distance: <?= round($p['distance'], 2) ?> km
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
let userLat = <?= $userLat ? $userLat : 'null' ?>;
let userLon = <?= $userLon ? $userLon : 'null' ?>;
const pharmacies = <?= $pharmacyJson ?>;

function detectLocation() {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported in this browser.");
        return;
    }
    navigator.geolocation.getCurrentPosition(
        pos => {
            const lat = pos.coords.latitude;
            const lon = pos.coords.longitude;
            const params = new URLSearchParams(window.location.search);
            params.set('lat', lat);
            params.set('lon', lon);
            window.location.search = params.toString();
        },
        () => alert("Please allow location access to find nearest pharmacies.")
    );
}

let initialLat = 42.6629;
let initialLon = 21.1655;
if (userLat && userLon) {
    initialLat = userLat;
    initialLon = userLon;
} else if (pharmacies.length > 0 && pharmacies[0].latitude && pharmacies[0].longitude) {
    initialLat = pharmacies[0].latitude;
    initialLon = pharmacies[0].longitude;
}

const map = L.map('map').setView([initialLat, initialLon], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

if (userLat && userLon) {
    L.marker([userLat, userLon], {title: "You"})
        .addTo(map)
        .bindPopup("You are here");
}

pharmacies.forEach(p => {
    if (!p.latitude || !p.longitude) return;
    let popup = "<b>" + p.name + "</b><br>" + (p.city || "");
    if (p.distance !== null) {
        popup += "<br>Distance: " + (Math.round(p.distance * 100) / 100) + " km";
    }
    L.marker([p.latitude, p.longitude]).addTo(map).bindPopup(popup);
});
</script>

</body>
</html>
