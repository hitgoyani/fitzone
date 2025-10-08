<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email FROM members WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | FitZone Gym</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        background: #111; 
        color: #fff;
    }

   
    header {
        background: #000; 
        padding: 25px 20px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        box-shadow: 0 4px 10px rgba(0,0,0,0.5);
    }

  
    nav {
        background: #000;
        padding: 15px;
        text-align: center;
    }
    nav a {
        color: #fff;
        margin: 0 15px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }
    nav a:hover {
        color: #aaa;
    }

   
    .container {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

   
    .card {
        background: #1c1c1c; 
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.7);
        transition: transform 0.3s, background 0.3s;
    }
    .card:hover {
        background: #333; 
        transform: translateY(-5px);
    }
    .card h2 {
        color: #fff;
        margin-bottom: 15px;
        font-size: 20px;
    }
    .card p, .card li {
        margin-bottom: 10px;
        line-height: 1.5;
    }
    .card ul {
        padding-left: 20px;
    }

  
    .btn {
        display: inline-block;
        padding: 10px 18px;
        background: #444;
        color: #fff;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        transition: background 0.3s, color 0.3s, transform 0.2s;
    }
    .btn:hover {
        background: #fff; 
        color: #000;
        transform: scale(1.05);
    }

   
    @media(max-width: 600px){
        header {
            font-size: 20px;
            padding: 20px 10px;
        }
        nav a {
            margin: 5px 10px;
        }
    }
</style>
</head>
<body>

<header>
    Welcome, <?php echo htmlspecialchars($user['name']); ?> 👋
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">

  
    <div class="card">
        <h2>Profile Overview</h2>
        <p><b>Email:</b> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><b>Phone:</b> <?php echo htmlspecialchars($user['phone']); ?></p>
        <p><b>Plan:</b> <?php echo htmlspecialchars($user['plan']); ?></p>
        <p><b>Membership Expiry:</b> <?php echo htmlspecialchars($user['membership_expiry']); ?></p>
        <a href="edit_profile.php" class="btn">Update Profile</a>
    </div>

    
    <div class="card">
        <h2>Workout & Progress</h2>
        <p>💪 Track your fitness journey here.</p>
        <a href="progress.php" class="btn">View Progress</a>
    </div>

    
    <div class="card">
        <h2>Class Schedule</h2>
        <p>📅 Check upcoming classes and events.</p>
        <a href="classes.php" class="btn">View Classes</a>
    </div>

    <div class="card">
        <h2>Notifications</h2>
        <ul>
            <li>🔥 New Zumba batch starts next week.</li>
            <li>⚡ Renew your membership before expiry.</li>
        </ul>
    </div>

</div>

</body>
</html>
