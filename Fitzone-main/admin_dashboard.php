<?php

session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$admin_name = 'Admin';
$role = '';

if (!empty($_SESSION['role'])) {
    $role = $_SESSION['role'];
    if (!empty($_SESSION['user_name'])) {
        $admin_name = $_SESSION['user_name'];
    }
} else {
    $user_id = (int)$_SESSION['user_id'];
    $stmtR = $conn->prepare("SELECT role, name FROM members WHERE id = ?");
    if ($stmtR) {
        $stmtR->bind_param("i", $user_id);
        $stmtR->execute();
        $resR = $stmtR->get_result();
        if ($rowR = $resR->fetch_assoc()) {
            $role = $rowR['role'] ?? '';
            $admin_name = $rowR['name'] ?? 'admin';
            $_SESSION['role'] = $role;
            $_SESSION['user_name'] = $admin_name;
        }
        $stmtR->close();
    } else {
        die("Database error: cannot verify admin role. Check members table.");
    }
}

if (strtolower($role) !== 'admin') {
    header("HTTP/1.1 403 Forbidden");
    echo "Access denied. Admins only. <a href=\"dashboard.php\">Go back</a>";
    exit();
}

$action_result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_member_id'])) {
    $del_id = (int)$_POST['delete_member_id'];

    $email = '';
    $stmtE = $conn->prepare("SELECT email FROM members WHERE id = ?");
    if ($stmtE) {
        $stmtE->bind_param("i", $del_id);
        $stmtE->execute();
        $resE = $stmtE->get_result();
        if ($rowE = $resE->fetch_assoc()) {
            $email = $rowE['email'];
        }
        $stmtE->close();
    }

    $stmtD = $conn->prepare("DELETE FROM members WHERE id = ?");
    if ($stmtD) {
        $stmtD->bind_param("i", $del_id);
        if ($stmtD->execute()) {
            if (!empty($email)) {
                $stmtX = $conn->prepare("DELETE FROM active_logins WHERE email = ?");
                if ($stmtX) {
                    $stmtX->bind_param("s", $email);
                    $stmtX->execute();
                    $stmtX->close();
                }
            }
            $action_result = "Member deleted successfully.";
        } else {
            $action_result = "Failed to delete member (DB error).";
        }
        $stmtD->close();
    } else {
        $action_result = "Delete failed: " . htmlspecialchars($conn->error);
    }
}

function safe_count($conn, $sql, $types = '', $params = []) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) return 0;
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) { $stmt->close(); return 0; }
    $res = $stmt->get_result();
    if (!$res) { $stmt->close(); return 0; }
    $row = $res->fetch_row();
    $stmt->close();
    return (int)$row[0];
}

$total_members = safe_count($conn, "SELECT COUNT(*) FROM members");
$active_members = safe_count($conn, "SELECT COUNT(*) FROM active_logins");
$total_trainers = safe_count($conn, "SELECT COUNT(*) FROM trainers");
$total_classes = safe_count($conn, "SELECT COUNT(*) FROM classes");

$active_memberships = 0;
$stmtM = $conn->prepare("SELECT COUNT(*) FROM members WHERE membership_expiry >= CURDATE()");
if ($stmtM) {
    $stmtM->execute();
    $resM = $stmtM->get_result();
    $rowM = $resM->fetch_row();
    $active_memberships = (int)$rowM[0];
    $stmtM->close();
}

$recent_members = [];
$stmtL = $conn->prepare("SELECT id, name, email, plan, membership_expiry FROM members ORDER BY id DESC LIMIT 10");
if ($stmtL) {
    $stmtL->execute();
    $resL = $stmtL->get_result();
    while ($r = $resL->fetch_assoc()) {
        $recent_members[] = $r;
    }
    $stmtL->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Admin Dashboard | FitZone</title>
<link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>

<div class="top-bar">
    <div class="brand">FitZone <span>Gym</span></div>
    <div class="top-actions">
        <div style="color:var(--muted);font-weight:600">Welcome, <?php echo htmlspecialchars($admin_name); ?> 🎖️</div>
        <a class="pill" href="index.php">View Site</a>
        <a class="pill" href="logout.php">Logout</a>
    </div>
</div>

<div class="layout">
    <aside class="sidebar">
        <h3>Admin Menu</h3>
        <nav class="nav">
            <a class="active" href="admin_dashboard.php">Dashboard</a>
            <a href="admin_members.php">Manage Members</a>
            <a href="admin_trainers.php">Manage Trainers</a>
            <a href="admin_classes.php">Manage Classes</a>
            <a href="admin_gallery.php">Gallery</a>
            <a href="admin_settings.php">Settings</a>
        </nav>
    </aside>

    <main class="content">
        <div class="row">
            <div class="card">
                <h4>Total Members</h4>
                <div class="stat"><?php echo $total_members; ?></div>
            </div>
            <div class="card">
                <h4>Active Now</h4>
                <div class="stat"><?php echo $active_members; ?></div>
            </div>
            <div class="card">
                <h4>Active Memberships</h4>
                <div class="stat"><?php echo $active_memberships; ?></div>
            </div>
            <div class="card">
                <h4>Total Trainers</h4>
                <div class="stat"><?php echo $total_trainers; ?></div>
            </div>
            <div class="card">
                <h4>Total Classes</h4>
                <div class="stat"><?php echo $total_classes; ?></div>
            </div>
        </div>

        <div class="panel">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                <h3 style="margin:0">Recent Members</h3>
                <form method="get" style="display:flex;gap:8px;align-items:center">
                    <input type="text" name="q" placeholder="Search by name or email" style="padding:8px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:var(--white)">
                    <button type="submit" class="btn-sm">Search</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Plan</th><th>Expiry</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if (count($recent_members) === 0): ?>
                        <tr><td colspan="5" style="color:var(--muted)">No members found.</td></tr>
                    <?php else: ?>
                        <?php foreach($recent_members as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['name']); ?></td>
                                <td><?php echo htmlspecialchars($m['email']); ?></td>
                                <td><?php echo htmlspecialchars($m['plan'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($m['membership_expiry'] ?? '-'); ?></td>
                                <td>
                                    <a class="btn-sm" href="admin_members_edit.php?id=<?php echo (int)$m['id']; ?>">Edit</a>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Delete this member?');">
                                        <input type="hidden" name="delete_member_id" value="<?php echo (int)$m['id']; ?>">
                                        <button type="submit" class="btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if ($action_result): ?>
                <p style="margin-top:12px;color:var(--green)"><?php echo htmlspecialchars($action_result); ?></p>
            <?php endif; ?>
        </div>

    </main>
</div>

</body>
</html>
<?php $conn->close(); ?>
