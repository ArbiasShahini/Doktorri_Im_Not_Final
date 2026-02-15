<?php
session_start();
require 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: doctors.php");
    exit;
}

$stmt = $conn->prepare("SELECT id, name, specialization, description, created_at FROM doctors WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (!$doc) {
    header("Location: doctors.php");
    exit;
}

$appsStmt = $conn->prepare("SELECT id, doctor_name, date, time, status FROM appointments WHERE doctor_name=? ORDER BY date DESC, time DESC LIMIT 5");
$appsStmt->bind_param("s", $doc['name']);
$appsStmt->execute();
$recentApps = $appsStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($doc['name']) ?> | MyDoctor</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body { background:#f8fafc; }
    .page { max-width:1100px; margin:40px auto 70px auto; padding:0 24px; }
    .grid { display:grid; grid-template-columns: 1.35fr 0.65fr; gap:18px; align-items:start; }
    .card { background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:18px; box-shadow:0 12px 25px rgba(15,23,42,.08); }
    .top { display:flex; gap:14px; align-items:center; }
    .avatar {
      width:64px; height:64px; border-radius:16px;
      background:linear-gradient(135deg,#2563eb,#1d4ed8);
      display:flex; align-items:center; justify-content:center;
      color:#fff; font-weight:700; font-size:22px;
    }
    .title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
    .subtitle { margin-top:4px; font-size:13px; color:#64748b; }
    .chips { display:flex; flex-wrap:wrap; gap:8px; margin-top:12px; }
    .chip { font-size:12px; padding:6px 10px; border-radius:999px; background:#eff6ff; color:#1d4ed8; border:1px solid #dbeafe; }
    .sectionTitle { margin:16px 0 8px; font-size:14px; font-weight:700; color:#0f172a; }
    .text { font-size:14px; color:#334155; line-height:1.6; }
    .cta { display:flex; gap:10px; margin-top:14px; flex-wrap:wrap; }
    .btnPrimary {
      padding:10px 16px; border-radius:999px; border:0;
      background:#2563eb; color:#fff; cursor:pointer; text-decoration:none; font-size:14px;
      display:inline-flex; align-items:center; justify-content:center;
    }
    .btnPrimary:hover { background:#1d4ed8; }
    .btnSecondary {
      padding:10px 16px; border-radius:999px; border:1px solid #d1d5db;
      background:#fff; color:#0f172a; cursor:pointer; text-decoration:none; font-size:14px;
      display:inline-flex; align-items:center; justify-content:center;
    }
    .btnSecondary:hover { background:#f1f5f9; }
    .infoRow { display:flex; justify-content:space-between; gap:12px; padding:10px 0; border-bottom:1px solid #eef2f7; }
    .infoRow:last-child { border-bottom:none; }
    .muted { color:#64748b; font-size:13px; }
    .badge {
      font-size:12px; padding:5px 10px; border-radius:999px; border:1px solid #e5e7eb; background:#f8fafc; color:#0f172a;
      display:inline-flex;
    }
    .badge.pending { background:#fff7ed; border-color:#fed7aa; color:#c2410c; }
    .badge.approved { background:#ecfdf5; border-color:#bbf7d0; color:#15803d; }
    .badge.rejected { background:#fef2f2; border-color:#fecaca; color:#b91c1c; }
    .badge.completed { background:#eff6ff; border-color:#dbeafe; color:#1d4ed8; }
    table { width:100%; border-collapse:collapse; }
    th, td { text-align:left; font-size:13px; padding:10px 8px; border-bottom:1px solid #eef2f7; vertical-align:top; }
    th { color:#64748b; font-weight:600; }
    .empty { font-size:13px; color:#64748b; padding:10px 0; }
    @media (max-width: 900px) { .grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page">
  <div class="grid">

    <div class="card">
      <div class="top">
        <div class="avatar"> 
          <?= strtoupper(substr($doc['name'], 0, 1)) ?>
        </div>
        <div>
          <h1 class="title"><?= htmlspecialchars($doc['name']) ?></h1>
          <div class="subtitle"><?= htmlspecialchars($doc['specialization']) ?></div>
          <div class="chips">
            <span class="chip">Verified Doctor</span>
            <span class="chip">MyDoctor Partner</span>
          </div>
        </div>
      </div>
      

      <div class="sectionTitle">About</div>
      <div class="text">
        <?= nl2br(htmlspecialchars($doc['description'])) ?>
      </div>

      <div class="cta">
        <a class="btnPrimary" href="schedule.php?doctor=<?= urlencode($doc['name']) ?>">Book appointment</a>
        <a class="btnSecondary" href="doctors.php">Back to doctors</a>
      </div>

      <div class="sectionTitle" style="margin-top:18px;">Recent appointments (preview)</div>
      <?php if ($recentApps && $recentApps->num_rows > 0): ?>
        <table>
          <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
          </tr>
          <?php while ($a = $recentApps->fetch_assoc()): ?>
            <?php
              $st = $a['status'] ?? 'pending';
              $cls = in_array($st, ['pending','approved','rejected','completed']) ? $st : '';
            ?>
            <tr>
              <td><?= htmlspecialchars($a['date']) ?></td>
              <td><?= htmlspecialchars(substr($a['time'],0,5)) ?></td>
              <td><span class="badge <?= $cls ?>"><?= htmlspecialchars($st) ?></span></td>
            </tr>
          <?php endwhile; ?>
        </table>
      <?php else: ?>
        <div class="empty">No appointments yet for this doctor.</div>
      <?php endif; ?>
    </div>

    <div class="card">
      <div class="sectionTitle" style="margin-top:0;">Quick info</div>

      <div class="infoRow">
        <div class="muted">Specialization</div>
        <div><?= htmlspecialchars($doc['specialization']) ?></div>
      </div>

      <div class="infoRow">
        <div class="muted">Availability</div>
        <div><span class="badge">Mon–Fri</span> <span class="badge">09:00–17:00</span></div>
      </div>

      <div class="infoRow">
        <div class="muted">Consultation</div>
        <div><span class="badge">In clinic</span> <span class="badge">Online</span></div>
      </div>

      <div class="infoRow">
        <div class="muted">Support</div>
        <div><span class="badge">24/7</span></div>
      </div>

      <div class="sectionTitle" style="margin-top:18px;">Need help?</div>
      <div class="text" style="font-size:13px;">
        You can ask a question using our AI assistant. If you are not satisfied, you can escalate to a human agent.
      </div>

      <div class="cta" style="margin-top:12px;">
        <a class="btnPrimary" href="ask_doctor.php">Ask Doctor</a>
        <a class="btnSecondary" href="pharmacy.php">Find pharmacy</a>
      </div>
    </div>

  </div>
</div>

</body>
</html>
