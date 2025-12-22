<?php
session_start();
require 'db.php';

if (empty($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit;
}

$tab = $_GET['tab'] ?? 'overview';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_user'])) {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        if ($id > 0 && $name !== '' && $email !== '') {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $email, $id);
            $stmt->execute();
        }
        header("Location: admin_dashboard.php?tab=users");
        exit;
    }

    if (isset($_POST['delete_user'])) {
        $id = intval($_POST['id']);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        header("Location: admin_dashboard.php?tab=users");
        exit;
    }

    if (isset($_POST['save_doctor'])) {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name']);
        $spec = trim($_POST['specialization']);
        $desc = trim($_POST['description']);
        if ($name !== '' && $spec !== '' && $desc !== '') {
            if ($id > 0) {
                $stmt = $conn->prepare("UPDATE doctors SET name = ?, specialization = ?, description = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $spec, $desc, $id);
            } else {
                $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, description) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $spec, $desc);
            }
            $stmt->execute();
        }
        header("Location: admin_dashboard.php?tab=doctors");
        exit;
    }

    if (isset($_POST['delete_doctor'])) {
        $id = intval($_POST['id']);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        header("Location: admin_dashboard.php?tab=doctors");
        exit;
    }

    if (isset($_POST['save_appointment'])) {
        $id = intval($_POST['id']);
        $doctor = trim($_POST['doctor_name']);
        $date = trim($_POST['date']);
        $time = trim($_POST['time']);
        if ($id > 0 && $doctor !== '' && $date !== '' && $time !== '') {
            $stmt = $conn->prepare("UPDATE appointments SET doctor_name = ?, date = ?, time = ? WHERE id = ?");
            $stmt->bind_param("sssi", $doctor, $date, $time, $id);
            $stmt->execute();
        }
        header("Location: admin_dashboard.php?tab=appointments");
        exit;
    }

    if (isset($_POST['delete_appointment'])) {
        $id = intval($_POST['id']);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        header("Location: admin_dashboard.php?tab=appointments");
        exit;
    }

    if (isset($_POST['delete_feedback'])) {
        $id = intval($_POST['id']);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        header("Location: admin_dashboard.php?tab=feedback");
        exit;
    }

    if (isset($_POST['save_medicine'])) {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);
        $loc = trim($_POST['location']);
        $phone = trim($_POST['phone']);
        if ($name !== '' && $desc !== '') {
            if ($id > 0) {
                $stmt = $conn->prepare("UPDATE medicines SET name = ?, description = ?, location = ?, phone = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $name, $desc, $loc, $phone, $id);
            } else {
                $stmt = $conn->prepare("INSERT INTO medicines (name, description, location, phone) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $desc, $loc, $phone);
            }
            $stmt->execute();
        }
        header("Location: admin_dashboard.php?tab=pharmacy");
        exit;
    }

    if (isset($_POST['delete_medicine'])) {
        $id = intval($_POST['id']);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM medicines WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        header("Location: admin_dashboard.php?tab=pharmacy");
        exit;
    }
}

$totalUsers = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'] ?? 0;
$totalAppointments = $conn->query("SELECT COUNT(*) AS c FROM appointments")->fetch_assoc()['c'] ?? 0;
$totalFeedback = $conn->query("SELECT COUNT(*) AS c FROM feedback")->fetch_assoc()['c'] ?? 0;
$avgRatingRow = $conn->query("SELECT AVG(rating) AS r FROM feedback")->fetch_assoc();
$avgRating = ($avgRatingRow && $avgRatingRow['r']) ? number_format($avgRatingRow['r'], 1) : 'N/A';

$latestUsers = $conn->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 6");
$latestApps = $conn->query("SELECT a.id, a.user_id, a.doctor_name, a.date, a.time, a.created_at, u.name AS user_name FROM appointments a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 6");
$latestFb = $conn->query("SELECT f.id, f.user_id, f.rating, f.comment, f.created_at, u.name AS user_name FROM feedback f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC LIMIT 6");

$editUserId = isset($_GET['edit_user']) ? intval($_GET['edit_user']) : 0;
$editDoctorId = isset($_GET['edit_doctor']) ? intval($_GET['edit_doctor']) : 0;
$editAppId = isset($_GET['edit_app']) ? intval($_GET['edit_app']) : 0;
$editMedId = isset($_GET['edit_med']) ? intval($_GET['edit_med']) : 0;

$editUser = null;
$editDoctor = null;
$editApp = null;
$editMed = null;

if ($editUserId > 0) {
    $res = $conn->query("SELECT * FROM users WHERE id = $editUserId");
    $editUser = $res ? $res->fetch_assoc() : null;
}
if ($editDoctorId > 0) {
    $res = $conn->query("SELECT * FROM doctors WHERE id = $editDoctorId");
    $editDoctor = $res ? $res->fetch_assoc() : null;
}
if ($editAppId > 0) {
    $res = $conn->query("SELECT * FROM appointments WHERE id = $editAppId");
    $editApp = $res ? $res->fetch_assoc() : null;
}
if ($editMedId > 0) {
    $res = $conn->query("SELECT * FROM medicines WHERE id = $editMedId");
    $editMed = $res ? $res->fetch_assoc() : null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MyDoctor Admin</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-body">
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-brand">
      <div class="brand-logo">MD</div>
      <div class="brand-text">
        <span class="brand-title">MyDoctor</span>
        <span class="brand-subtitle">Admin console</span>
      </div>
    </div>
    <nav class="admin-menu">
      <a href="admin_dashboard.php?tab=overview" class="menu-item <?= $tab==='overview'?'active':'' ?>">
        <span class="menu-dot"></span>
        <span>Overview</span>
      </a>
      <a href="admin_dashboard.php?tab=users" class="menu-item <?= $tab==='users'?'active':'' ?>">
        <span class="menu-dot"></span>
        <span>Users</span>
      </a>
      <a href="admin_dashboard.php?tab=appointments" class="menu-item <?= $tab==='appointments'?'active':'' ?>">
        <span class="menu-dot"></span>
        <span>Appointments</span>
      </a>
      <a href="admin_dashboard.php?tab=doctors" class="menu-item <?= $tab==='doctors'?'active':'' ?>">
        <span class="menu-dot"></span>
        <span>Doctors</span>
      </a>
      <a href="admin_dashboard.php?tab=pharmacy" class="menu-item <?= $tab==='pharmacy'?'active':'' ?>">
        <span class="menu-dot"></span>
        <span>Pharmacy</span>
      </a>
      <a href="admin_dashboard.php?tab=feedback" class="menu-item <?= $tab==='feedback'?'active':'' ?>">
        <span class="menu-dot"></span>
        <span>Feedback</span>
      </a>
    </nav>
    <div class="admin-menu admin-menu-bottom">
      <a href="index.php" class="menu-item">
        <span class="menu-dot"></span>
        <span>Back to site</span>
      </a>
      <a href="handle_logout.php" class="menu-item">
        <span class="menu-dot danger"></span>
        <span>Logout</span>
      </a>
    </div>
  </aside>

  <main class="admin-main">
    <header class="admin-topbar">
      <div>
        <div class="topbar-title">Admin dashboard</div>
        <div class="topbar-subtitle">Operational view of users, appointments, doctors, pharmacy and feedback</div>
      </div>
      <div class="topbar-meta">
        <span class="topbar-pill">Role: Admin</span>
        <span class="topbar-pill muted"><?= date('d M Y') ?></span>
      </div>
    </header>

    <section class="admin-content">
      <?php if ($tab === 'overview'): ?>

        <div class="grid-4">
          <div class="metric-card">
            <div class="metric-label">Total users</div>
            <div class="metric-value"><?= $totalUsers ?></div>
          </div>
          <div class="metric-card">
            <div class="metric-label">Total appointments</div>
            <div class="metric-value"><?= $totalAppointments ?></div>
          </div>
          <div class="metric-card">
            <div class="metric-label">Total feedback</div>
            <div class="metric-value"><?= $totalFeedback ?></div>
          </div>
          <div class="metric-card">
            <div class="metric-label">Average rating</div>
            <div class="metric-value"><?= $avgRating ?></div>
          </div>
        </div>

        <div class="grid-2">
          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="panel-title">Users</div>
                <div class="panel-subtitle">Latest registered users</div>
              </div>
            </div>
            <div class="table-wrapper">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                <?php if ($latestUsers && $latestUsers->num_rows): ?>
                  <?php while ($u = $latestUsers->fetch_assoc()): ?>
                    <tr>
                      <td><?= $u['id'] ?></td>
                      <td><?= htmlspecialchars($u['name']) ?></td>
                      <td><?= htmlspecialchars($u['email']) ?></td>
                      <td><?= $u['created_at'] ?></td>
                      <td class="table-actions">
                        <a href="admin_dashboard.php?tab=users&edit_user=<?= $u['id'] ?>" class="btn-chip">Edit</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="5" class="empty-row">No users yet.</td></tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="panel-title">Latest appointments</div>
                <div class="panel-subtitle">Most recent bookings in the system</div>
              </div>
            </div>
            <div class="table-wrapper">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Created</th>
                  </tr>
                </thead>
                <tbody>
                <?php if ($latestApps && $latestApps->num_rows): ?>
                  <?php while ($a = $latestApps->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($a['user_name'] ?: ('User '.$a['user_id'])) ?></td>
                      <td><?= htmlspecialchars($a['doctor_name']) ?></td>
                      <td><?= $a['date'] ?></td>
                      <td><?= substr($a['time'],0,5) ?></td>
                      <td><?= $a['created_at'] ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="5" class="empty-row">No appointments.</td></tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-header">
            <div>
              <div class="panel-title">Latest feedback</div>
              <div class="panel-subtitle">What patients recently shared</div>
            </div>
          </div>
          <div class="table-wrapper">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Rating</th>
                  <th>Comment</th>
                  <th>Created</th>
                </tr>
              </thead>
              <tbody>
              <?php if ($latestFb && $latestFb->num_rows): ?>
                <?php while ($f = $latestFb->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($f['user_name'] ?: ('User '.$f['user_id'])) ?></td>
                    <td><?= $f['rating'] ?>/5</td>
                    <td><?= htmlspecialchars($f['comment']) ?></td>
                    <td><?= $f['created_at'] ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="4" class="empty-row">No feedback yet.</td></tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      <?php elseif ($tab === 'users'): ?>

        <?php $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC"); ?>

        <div class="grid-2">
          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="panel-title">Users</div>
                <div class="panel-subtitle">All registered accounts</div>
              </div>
            </div>
            <div class="table-wrapper">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th></th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                <?php if ($users && $users->num_rows): ?>
                  <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                      <td><?= $u['id'] ?></td>
                      <td><?= htmlspecialchars($u['name']) ?></td>
                      <td><?= htmlspecialchars($u['email']) ?></td>
                      <td><?= $u['created_at'] ?></td>
                      <td class="table-actions">
                        <a href="admin_dashboard.php?tab=users&edit_user=<?= $u['id'] ?>" class="btn-chip">Edit</a>
                      </td>
                      <td class="table-actions">
                        <form method="post" onsubmit="return confirm('Delete this user?')">
                          <input type="hidden" name="id" value="<?= $u['id'] ?>">
                          <button class="btn-chip danger" name="delete_user" type="submit">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="empty-row">No users in the system.</td></tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="panel-title"><?= $editUser ? 'Edit user' : 'User details' ?></div>
                <div class="panel-subtitle"><?= $editUser ? 'Update profile information' : 'Select a user from the table' ?></div>
              </div>
            </div>
            <?php if ($editUser): ?>
              <form method="post" class="form-vertical">
                <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
                <label class="field-label">Name</label>
                <input class="field-input" name="name" value="<?= htmlspecialchars($editUser['name']) ?>">
                <label class="field-label">Email</label>
                <input class="field-input" name="email" value="<?= htmlspecialchars($editUser['email']) ?>">
                <div class="form-actions">
                  <button class="btn-primary" name="save_user" type="submit">Save changes</button>
                  <a href="admin_dashboard.php?tab=users" class="btn-secondary">Cancel</a>
                </div>
              </form>
            <?php else: ?>
              <div class="empty-panel">Choose a user from the table to edit details.</div>
            <?php endif; ?>
          </div>
        </div>

      <?php elseif ($tab === 'appointments'): ?>

        <?php
        $apps = $conn->query("SELECT a.*, u.name AS user_name FROM appointments a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.date DESC, a.time DESC");
        ?>

        <div class="grid-2">
          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="panel-title">Appointments</div>
                <div class="panel-subtitle">All booked visits</div>
              </div>
            </div>
            <div class="table-wrapper">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th></th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                <?php if ($apps && $apps->num_rows): ?>
                  <?php while ($a = $apps->fetch_assoc()): ?>
                    <tr>
                      <td><?= $a['id'] ?></td>
                      <td><?= htmlspecialchars($a['user_name'] ?: ('User '.$a['user_id'])) ?></td>
                      <td><?= htmlspecialchars($a['doctor_name']) ?></td>
                      <td><?= $a['date'] ?></td>
                      <td><?= substr($a['time'],0,5) ?></td>
                      <td class="table-actions">
                        <a href="admin_dashboard.php?tab=appointments&edit_app=<?= $a['id'] ?>" class="btn-chip">Edit</a>
                      </td>
                      <td class="table-actions">
                        <form method="post" onsubmit="return confirm('Delete this appointment?')">
                          <input type="hidden" name="id" value="<?= $a['id'] ?>">
                          <button class="btn-chip danger" name="delete_appointment" type="submit">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="7" class="empty-row">No appointments in the system.</td></tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="panel-title"><?= $editApp ? 'Edit appointment' : 'Appointment details' ?></div>
                <div class="panel-subtitle"><?= $editApp ? 'Adjust booking information' : 'Select a row on the left to edit' ?></div>
              </div>
            </div>
            <?php if ($editApp): ?>
              <form method="post" class="form-vertical">
                <input type="hidden" name="id" value="<?= $editApp['id'] ?>">
                <label class="field-label">Doctor name</label>
                <input class="field-input" name="doctor_name" value="<?= htmlspecialchars($editApp['doctor_name']) ?>">
                <label class="field-label">Date</label>
                <input class="field-input" type="date" name="date" value="<?= $editApp['date'] ?>">
                <label class="field-label">Time</label>
                <input class="field-input" type="time" name="time" value="<?= substr($editApp['time'],0,5) ?>">
                <div class="form-actions">
                  <button class="btn-primary" name="save_appointment" type="submit">Save changes</button>
                  <a href="admin_dashboard.php?tab=appointments" class="btn-secondary">Cancel</a>
                </div>
              </form>
            <?php else: ?>
              <div class="empty-panel">Choose an appointment to see details.</div>
            <?php endif; ?>
          </div>
        </div>

      <?php elseif ($tab === 'doctors'): ?>

        <?php $docs = $conn->query("SELECT * FROM doctors ORDER BY created_at DESC"); ?>

        <div class="grid-2">
          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="panel-title">Doctors</div>
                <div class="panel-subtitle">Profiles visible to patients</div>
              </div>
            </div>
            <div class="table-wrapper">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Description</th>
                    <th></th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                <?php if ($docs && $docs->num_rows): ?>
                  <?php while ($d = $docs->fetch_assoc()): ?>
                    <tr>
                      <td><?= $d['id'] ?></td>
                      <td><?= htmlspecialchars($d['name']) ?></td>
                      <td><?= htmlspecialchars($d['specialization']) ?></td>
                      <td class="truncate"><?= htmlspecialchars($d['description']) ?></td>
                      <td class="table-actions">
                        <a href="admin_dashboard.php?tab=doctors&edit_doctor=<?= $d['id'] ?>" class="btn-chip">Edit</a>
                      </td>
                      <td class="table-actions">
                        <form method="post" onsubmit="return confirm('Delete this doctor?')">
                          <input type="hidden" name="id" value="<?= $d['id'] ?>">
                          <button class="btn-chip danger" name="delete_doctor" type="submit">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="empty-row">No doctors registered.</td></tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="panel-title"><?= $editDoctor ? 'Edit doctor' : 'Add doctor' ?></div>
                <div class="panel-subtitle"><?= $editDoctor ? 'Update doctor profile' : 'Create a new doctor profile' ?></div>
              </div>
            </div>
            <form method="post" class="form-vertical">
              <input type="hidden" name="id" value="<?= $editDoctor['id'] ?? 0 ?>">
              <label class="field-label">Name</label>
              <input class="field-input" name="name" value="<?= htmlspecialchars($editDoctor['name'] ?? '') ?>">
              <label class="field-label">Specialization</label>
              <input class="field-input" name="specialization" value="<?= htmlspecialchars($editDoctor['specialization'] ?? '') ?>">
              <label class="field-label">Description</label>
              <textarea class="field-textarea" name="description"><?= htmlspecialchars($editDoctor['description'] ?? '') ?></textarea>
              <div class="form-actions">
                <button class="btn-primary" name="save_doctor" type="submit">Save</button>
                <?php if ($editDoctor): ?>
                  <a href="admin_dashboard.php?tab=doctors" class="btn-secondary">Cancel</a>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>

      <?php elseif ($tab === 'pharmacy'): ?>

        <?php $meds = $conn->query("SELECT * FROM medicines ORDER BY created_at DESC"); ?>

        <div class="grid-2">
          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="panel-title">Pharmacy items</div>
                <div class="panel-subtitle">Medicines and pharmacy locations</div>
              </div>
            </div>
            <div class="table-wrapper">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Phone</th>
                    <th></th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                <?php if ($meds && $meds->num_rows): ?>
                  <?php while ($m = $meds->fetch_assoc()): ?>
                    <tr>
                      <td><?= $m['id'] ?></td>
                      <td><?= htmlspecialchars($m['name']) ?></td>
                      <td class="truncate"><?= htmlspecialchars($m['description']) ?></td>
                      <td><?= htmlspecialchars($m['location']) ?></td>
                      <td><?= htmlspecialchars($m['phone']) ?></td>
                      <td class="table-actions">
                        <a href="admin_dashboard.php?tab=pharmacy&edit_med=<?= $m['id'] ?>" class="btn-chip">Edit</a>
                      </td>
                      <td class="table-actions">
                        <form method="post" onsubmit="return confirm('Delete this medicine?')">
                          <input type="hidden" name="id" value="<?= $m['id'] ?>">
                          <button class="btn-chip danger" name="delete_medicine" type="submit">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="7" class="empty-row">No medicines defined.</td></tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="panel-title"><?= $editMed ? 'Edit medicine' : 'Add medicine' ?></div>
                <div class="panel-subtitle"><?= $editMed ? 'Update pharmacy entry' : 'Register a new medicine' ?></div>
              </div>
            </div>
            <form method="post" class="form-vertical">
              <input type="hidden" name="id" value="<?= $editMed['id'] ?? 0 ?>">
              <label class="field-label">Name</label>
              <input class="field-input" name="name" value="<?= htmlspecialchars($editMed['name'] ?? '') ?>">
              <label class="field-label">Description</label>
              <textarea class="field-textarea" name="description"><?= htmlspecialchars($editMed['description'] ?? '') ?></textarea>
              <label class="field-label">Location</label>
              <input class="field-input" name="location" value="<?= htmlspecialchars($editMed['location'] ?? '') ?>">
              <label class="field-label">Phone</label>
              <input class="field-input" name="phone" value="<?= htmlspecialchars($editMed['phone'] ?? '') ?>">
              <div class="form-actions">
                <button class="btn-primary" name="save_medicine" type="submit">Save</button>
                <?php if ($editMed): ?>
                  <a href="admin_dashboard.php?tab=pharmacy" class="btn-secondary">Cancel</a>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>

      <?php elseif ($tab === 'feedback'): ?>

        <?php
        $fbs = $conn->query("SELECT f.*, u.name AS user_name FROM feedback f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");
        ?>

        <div class="panel">
          <div class="panel-header">
            <div>
              <div class="panel-title">Feedback</div>
              <div class="panel-subtitle">All ratings and comments from patients</div>
            </div>
          </div>
          <div class="table-wrapper">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Rating</th>
                  <th>Comment</th>
                  <th>Created</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              <?php if ($fbs && $fbs->num_rows): ?>
                <?php while ($f = $fbs->fetch_assoc()): ?>
                  <tr>
                    <td><?= $f['id'] ?></td>
                    <td><?= htmlspecialchars($f['user_name'] ?: ('User '.$f['user_id'])) ?></td>
                    <td><?= $f['rating'] ?>/5</td>
                    <td class="truncate"><?= htmlspecialchars($f['comment']) ?></td>
                    <td><?= $f['created_at'] ?></td>
                    <td class="table-actions">
                      <form method="post" onsubmit="return confirm('Delete this feedback?')">
                        <input type="hidden" name="id" value="<?= $f['id'] ?>">
                        <button class="btn-chip danger" name="delete_feedback" type="submit">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="6" class="empty-row">No feedback has been submitted.</td></tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      <?php endif; ?>
    </section>

    <footer class="admin-footer">
      <span>MyDoctor Admin</span>
      <span class="dot-separator"></span>
      <span><?= date('Y') ?></span>
    </footer>
  </main>
</div>
</body>
</html>
