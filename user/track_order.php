<?php
session_start();
include('../includes/connection.php');

// Must be logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    header('Location: user_login.php');
    exit();
}

if (!isset($_GET['order_id'])) {
    header('Location: profile.php');
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id  = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? '';

// Get user email from session
if ($user_id) {
    $uq = mysqli_prepare($con, "SELECT email FROM `user` WHERE user_id = ?");
    mysqli_stmt_bind_param($uq, 'i', $user_id);
} else {
    $uq = mysqli_prepare($con, "SELECT email FROM `user` WHERE username = ?");
    mysqli_stmt_bind_param($uq, 's', $username);
}
mysqli_stmt_execute($uq);
$ur = mysqli_stmt_get_result($uq);
$urow = mysqli_fetch_assoc($ur);
$user_email = $urow['email'] ?? '';

// Fetch order — ONLY if it belongs to this user (secure by email)
$stmt = mysqli_prepare($con, "SELECT * FROM orders WHERE order_id = ? AND email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'is', $order_id, $user_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    die("<div class='container my-5 text-center'>
            <i class='fa-solid fa-circle-xmark text-danger' style='font-size:3rem;'></i>
            <h4 class='mt-3'>Order not found or access denied.</h4>
            <a href='profile.php' class='btn btn-success mt-3 rounded-pill px-4'>Back to Profile</a>
         </div>");
}

$order  = mysqli_fetch_assoc($result);
// Calculate total amount from order_items
$total_calculated = 0;

$total_stmt = mysqli_prepare(
    $con,
    "SELECT COALESCE(SUM(price * quantity), 0) AS total
     FROM order_items
     WHERE order_id = ?"
);

mysqli_stmt_bind_param($total_stmt, 'i', $order_id);
mysqli_stmt_execute($total_stmt);

$total_result = mysqli_stmt_get_result($total_stmt);
$total_row = mysqli_fetch_assoc($total_result);

$total_calculated = (float)($total_row['total'] ?? 0);

$status = strtolower($order['order_status'] ?? 'pending');


// Store location (Ganesh Store, Satara)
$store_lat = 17.6805;
$store_lng = 74.0183;

// Step progress mapping
$steps = ['pending'=>1,'confirmed'=>2,'processing'=>2,'shipped'=>3,'out for delivery'=>4,'delivered'=>5,'cancelled'=>0];
$current_step = $steps[$status] ?? 1;

// Progress bar %
$prog_pct = match($current_step) { 1=>0, 2=>25, 3=>50, 4=>75, 5=>100, default=>0 };

// Status badge color
$badge_map = ['delivered'=>'success','shipped'=>'primary','cancelled'=>'danger'];
$b = $badge_map[$status] ?? 'warning';

// Fetch ordered items
$items_q = mysqli_query($con, "SELECT oi.*, p.product_name, p.product_image
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order #<?php echo $order_id; ?> | Ganesh Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        :root {
            --primary:#059669; --primary-light:#10b981;
            --bg:#f0fdf4; --surface:#fff; --text:#0f172a;
            --muted:#64748b; --border:#e2e8f0;
            --font-heading:'Outfit',sans-serif; --font-sans:'Plus Jakarta Sans',sans-serif;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:var(--font-sans);background:var(--bg);color:var(--text);min-height:100vh}
        .track-header{background:#fff;border-bottom:1px solid var(--border);padding:14px 24px;
            display:flex;align-items:center;justify-content:space-between;
            position:sticky;top:0;z-index:100;box-shadow:0 2px 10px rgba(0,0,0,.05)}
        .brand-link{display:flex;align-items:center;gap:10px;text-decoration:none}
        .brand-name{font-family:var(--font-heading);font-size:1.15rem;font-weight:700;color:var(--text)}
        .brand-name span{color:var(--primary)}
        .track-container{max-width:1100px;margin:0 auto;padding:28px 16px 60px}
        .order-info-bar{background:#fff;border:1px solid var(--border);border-radius:14px;
            padding:18px 24px;display:flex;align-items:center;justify-content:space-between;
            flex-wrap:wrap;gap:12px;margin-bottom:24px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
        .order-id-badge{font-family:var(--font-heading);font-size:1.2rem;font-weight:800}
        .order-id-badge span{color:var(--primary)}
        .live-dot{width:10px;height:10px;border-radius:50%;background:#10b981;
            display:inline-block;animation:pulse-dot 1.5s infinite;margin-right:6px}
        @keyframes pulse-dot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(1.4)}}
        .timeline-wrapper{background:#fff;border:1px solid var(--border);border-radius:14px;
            padding:28px 24px;margin-bottom:24px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
        .timeline-steps{display:flex;align-items:flex-start;justify-content:space-between;position:relative}
        .timeline-steps::before{content:'';position:absolute;top:20px;left:10%;right:10%;
            height:3px;background:var(--border);z-index:0}
        .timeline-progress{position:absolute;top:20px;left:10%;height:3px;
            background:linear-gradient(90deg,var(--primary),var(--primary-light));
            z-index:1;transition:width 1s ease;border-radius:2px;width:0}
        .step{display:flex;flex-direction:column;align-items:center;text-align:center;
            position:relative;z-index:2;flex:1}
        .step-circle{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;
            justify-content:center;font-size:1rem;background:#e2e8f0;color:#94a3b8;
            border:3px solid #e2e8f0;transition:all .5s ease}
        .step.done .step-circle{background:var(--primary);color:#fff;border-color:var(--primary);
            box-shadow:0 0 0 4px rgba(16,185,129,.15)}
        .step.active .step-circle{background:var(--primary);color:#fff;border-color:var(--primary);
            animation:ring-pulse 1.5s infinite}
        @keyframes ring-pulse{0%,100%{box-shadow:0 0 0 4px rgba(16,185,129,.2)}
            50%{box-shadow:0 0 0 10px rgba(16,185,129,.06)}}
        .step.cancelled .step-circle{background:#ef4444;color:#fff;border-color:#ef4444}
        .step-label{font-size:.72rem;font-weight:600;color:var(--muted);margin-top:8px;
            line-height:1.3;max-width:70px}
        .step.done .step-label,.step.active .step-label{color:var(--primary)}
        #delivery-map{height:360px;border-radius:0}
        .map-card{background:#fff;border-radius:14px;border:1px solid var(--border);
            overflow:hidden;margin-bottom:24px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
        .map-card-header{padding:14px 20px;border-bottom:1px solid var(--border);
            display:flex;align-items:center;gap:10px;font-weight:700;
            font-family:var(--font-heading)}
        .detail-card{background:#fff;border:1px solid var(--border);border-radius:14px;
            padding:20px 24px;margin-bottom:20px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
        .detail-card h6{font-family:var(--font-heading);font-weight:700;font-size:1rem;margin-bottom:14px}
        .detail-row{display:flex;justify-content:space-between;padding:8px 0;
            border-bottom:1px solid var(--border);font-size:.875rem}
        .detail-row:last-child{border-bottom:none}
        .detail-label{color:var(--muted)}
        .detail-value{font-weight:600}
        .item-row{display:flex;align-items:center;gap:12px;padding:10px 0;
            border-bottom:1px solid var(--border)}
        .item-row:last-child{border-bottom:none}
        .item-img{width:48px;height:48px;object-fit:contain;border-radius:8px;
            border:1px solid var(--border);background:#f8fafc;padding:4px}
        .item-name{font-weight:600;font-size:.875rem}
        .item-qty{font-size:.78rem;color:var(--muted)}
        .refresh-badge{background:#f0fdf4;border:1px solid #bbf7d0;color:var(--primary);
            font-size:.75rem;font-weight:600;padding:4px 12px;border-radius:999px;
            display:inline-flex;align-items:center;gap:6px}
        @media(max-width:576px){
            .step-label{font-size:.62rem;max-width:55px}
            .step-circle{width:34px;height:34px;font-size:.8rem}
            .timeline-steps::before,.timeline-progress{top:17px}
        }
    </style>
</head>
<body>
<header class="track-header">
    <a href="../index.php" class="brand-link">
        <img src="../images/ganesh_store_logo__1_-removebg-preview.png" alt="Logo"
             style="width:36px;height:36px;" onerror="this.src='../images/ganesh store logo.png'">
        <span class="brand-name">Ganesh<span>Store</span></span>
    </a>
    <div class="d-flex align-items-center gap-3">
        <span class="refresh-badge"><span class="live-dot"></span> Live Tracking</span>
        <a href="profile.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> My Orders
        </a>
    </div>
</header>

<div class="track-container">

    <!-- Order Info Bar -->
    <div class="order-info-bar">
        <div>
            <div class="order-id-badge">Order <span>#<?php echo $order_id; ?></span></div>
            <div class="text-muted small mt-1">
                <i class="fa-regular fa-calendar me-1"></i>
                Placed on <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?>
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-<?php echo $b; ?> <?php echo $b==='warning'?'text-dark':''; ?> px-3 py-2 rounded-pill fs-6">
                <?php echo htmlspecialchars($order['order_status']); ?>
            </span>
            <div class="text-muted small mt-1"><i class="fa-solid fa-rotate me-1"></i>Auto-refresh 30s</div>
        </div>
    </div>

    <?php if ($status === 'cancelled'): ?>
    <div class="alert alert-danger rounded-3 d-flex align-items-center gap-3 mb-4">
        <i class="fa-solid fa-ban fs-4"></i>
        <div><strong>Order Cancelled.</strong>
        <a href="../index.php" class="ms-2 fw-bold text-danger">Shop Again &rarr;</a></div>
    </div>
    <?php endif; ?>

    <!-- Timeline -->
    <div class="timeline-wrapper">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="fw-bold m-0" style="font-family:var(--font-heading)">
                <i class="fa-solid fa-route text-success me-2"></i>Delivery Progress
            </h6>
            <small class="text-muted">Step <?php echo min($current_step,5); ?> of 5</small>
        </div>
        <div class="timeline-steps" id="timeline">
            <div class="timeline-progress" id="prog-bar"></div>
            <?php
            $ts = [
                ['fa-clipboard-check','Order Placed'],
                ['fa-box',            'Confirmed'],
                ['fa-truck',          'Out for Delivery'],
                ['fa-map-marker-alt', 'Nearby'],
                ['fa-house-chimney',  'Delivered'],
            ];
            foreach ($ts as $i => [$icon, $label]):
                $snum = $i + 1;
                if ($status === 'cancelled') $cls = 'cancelled';
                elseif ($snum < $current_step) $cls = 'done';
                elseif ($snum === $current_step) $cls = 'active';
                else $cls = '';
            ?>
            <div class="step <?php echo $cls; ?>">
                <div class="step-circle">
                    <?php if ($snum < $current_step && $status !== 'cancelled'): ?>
                        <i class="fa-solid fa-check"></i>
                    <?php else: ?>
                        <i class="fa-solid <?php echo $icon; ?>"></i>
                    <?php endif; ?>
                </div>
                <div class="step-label"><?php echo $label; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Map -->
        <div class="col-lg-7">
            <div class="map-card">
                <div class="map-card-header">
                    <i class="fa-solid fa-map-location-dot text-success"></i>
                    Live Delivery Map
                    <span class="ms-auto text-muted small fw-normal">
                        <span class="live-dot"></span> Tracking active
                    </span>
                </div>
                <div id="delivery-map"></div>
                <div class="p-3 border-top d-flex gap-3 small text-muted">
                    <span><span style="display:inline-block;width:12px;height:12px;background:#059669;border-radius:50%"></span> Your Address</span>
                    <span><span style="display:inline-block;width:12px;height:12px;background:#f59e0b;border-radius:50%"></span> Ganesh Store</span>
                    <?php if (in_array($status, ['shipped','out for delivery'])): ?>
                    <span><span style="display:inline-block;width:12px;height:12px;background:#0284c7;border-radius:50%"></span> Delivery Agent</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="col-lg-5">
            <!-- Delivery Address -->
            <div class="detail-card">
                <h6><i class="fa-solid fa-location-dot text-success me-2"></i>Delivery Address</h6>
                <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value"><?php echo htmlspecialchars($order['first_name'].' '.$order['last_name']); ?></span></div>
                <div class="detail-row"><span class="detail-label">Address</span><span class="detail-value"><?php echo htmlspecialchars($order['address1']); ?></span></div>
                <?php if (!empty($order['address2'])): ?>
                <div class="detail-row"><span class="detail-label">Address 2</span><span class="detail-value"><?php echo htmlspecialchars($order['address2']); ?></span></div>
                <?php endif; ?>
                <div class="detail-row"><span class="detail-label">City</span><span class="detail-value"><?php echo htmlspecialchars($order['city']); ?></span></div>
                <div class="detail-row"><span class="detail-label">State</span><span class="detail-value"><?php echo htmlspecialchars($order['state']); ?></span></div>
                <div class="detail-row"><span class="detail-label">Pincode</span><span class="detail-value"><?php echo htmlspecialchars($order['postcode']); ?></span></div>
                <div class="detail-row"><span class="detail-label">Phone</span><span class="detail-value"><?php echo htmlspecialchars($order['phone']); ?></span></div>
            </div>

            <!-- Order Summary -->
            <div class="detail-card">
                <h6><i class="fa-solid fa-receipt text-success me-2"></i>Order Summary</h6>
                <div class="detail-row"><span class="detail-label">Order ID</span><span class="detail-value"><?php echo $order_id; ?></span></div>
                <div class="detail-row"><span class="detail-label">Payment</span><span class="detail-value"><?php echo htmlspecialchars($order['payment_mode']); ?></span></div>
                <div class="detail-row">
    <span class="detail-label">Total</span>
    <span class="detail-value text-success fw-bold">
        ₹<?php echo number_format($total_calculated, 2); ?>
    </span>
</div>
                <div class="detail-row"><span class="detail-label">Status</span>
                    <span class="badge bg-<?php echo $b; ?> <?php echo $b==='warning'?'text-dark':''; ?> rounded-pill">
                        <?php echo htmlspecialchars($order['order_status']); ?>
                    </span>
                </div>
            </div>

            <!-- Items -->
            <?php if ($items_q && mysqli_num_rows($items_q) > 0): ?>
            <div class="detail-card">
                <h6><i class="fa-solid fa-basket-shopping text-success me-2"></i>Items Ordered</h6>
                <?php while ($item = mysqli_fetch_assoc($items_q)): ?>
                <div class="item-row">
                    <img src="../Admin/product_images/<?php echo htmlspecialchars($item['product_image']); ?>"
                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                         class="item-img" onerror="this.src='../images/ganesh store logo.png'">
                    <div class="flex-grow-1">
                        <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                        <div class="item-qty">Qty: <?php echo $item['quantity']; ?> &nbsp;|&nbsp; ₹<?php echo number_format($item['price'] ?? 0, 2); ?></div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Progress bar animation
setTimeout(() => {
    const bar = document.getElementById('prog-bar');
    if (bar) bar.style.width = `calc(<?php echo $prog_pct; ?>% * 0.8)`;
}, 300);

// Map
const storeLat = <?php echo $store_lat; ?>;
const storeLng = <?php echo $store_lng; ?>;
const delivAddr = "<?php echo addslashes($order['address1'].', '.$order['city'].', '.$order['state'].', India'); ?>";
const isShipped = <?php echo in_array($status,['shipped','out for delivery'])?'true':'false'; ?>;

const map = L.map('delivery-map', { zoomControl:true, scrollWheelZoom:false });
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom:19
}).addTo(map);

const mkHtml = (color, faClass) =>
    `<div style="background:${color};width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:15px;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3)"><i class="fa-solid ${faClass}"></i></div>`;

const icon = (color, faClass) => L.divIcon({html:mkHtml(color,faClass),className:'',iconSize:[36,36],iconAnchor:[18,18]});

const storeMarker = L.marker([storeLat,storeLng],{icon:icon('#f59e0b','fa-store')}).addTo(map);
storeMarker.bindPopup('<strong>&#128230; Ganesh Store</strong><br>Satara, Maharashtra').openPopup();

fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(delivAddr)}&limit=1`,
    { headers:{'Accept-Language':'en'} })
    .then(r => r.json())
    .then(data => {
        let dLat = data && data.length > 0 ? parseFloat(data[0].lat) : storeLat + 0.015;
        let dLng = data && data.length > 0 ? parseFloat(data[0].lon) : storeLng + 0.020;

        const delivMarker = L.marker([dLat,dLng],{icon:icon('#059669','fa-house')}).addTo(map);
        delivMarker.bindPopup(`<strong>&#127968; Your Address</strong><br>${delivAddr}`);

        L.polyline([[storeLat,storeLng],[dLat,dLng]],
            {color:'#059669',weight:3,dashArray:'8,6',opacity:.7}).addTo(map);

        map.fitBounds([[storeLat,storeLng],[dLat,dLng]],{padding:[50,50]});

        if (isShipped) {
            const tHtml = `<div style="background:#0284c7;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);animation:tp 1.2s infinite"><i class="fa-solid fa-truck-fast"></i></div>`;
            const truckIcon = L.divIcon({html:tHtml,className:'',iconSize:[38,38],iconAnchor:[19,19]});
            const t = L.marker([(storeLat+dLat)/2,(storeLng+dLng)/2],{icon:truckIcon}).addTo(map);
            t.bindPopup('<strong>&#128666; Delivery Agent</strong><br>On the way!');
        }
    }).catch(() => map.setView([storeLat,storeLng],14));

// Auto-refresh
let c = 30;
setInterval(()=>{ c--; if(c<=0) location.reload(); },1000);
</script>
<style>
@keyframes tp{0%,100%{transform:scale(1)}50%{transform:scale(1.1)}}
</style>
</body>
</html>