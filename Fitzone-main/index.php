<?php
session_start();
include("db.php"); 


$logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
  <title>FitZone Gym</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="top-bar">
    <div class="auth-buttons">
        <h1>FitZone</h1>
    </div>
    <div class="auth-buttons">
        <?php if (!$logged_in): ?>
            <button class="btn"><a href="register.html">Register</a></button>
            <button class="btn"><a href="login.html">Login</a></button>
        <?php else: ?>
            <span>Welcome, <?php echo $_SESSION['user_name']; ?>! 🎉</span>
            <button class="btn"><a href="dashboard.php">Dashboard</a></button>
            <button class="btn"><a href="logout.php">Logout</a></button>
        <?php endif; ?>
        
    </div>
</div>

<header>
    <h1>Welcome to FitZone Gym</h1>
    <p>"Transform Your Body, Elevate Your Life."</p>
</header>

<nav class="navbar">
    <a href="index.php">Home</a> |
    <a href="aboutus.html">About us</a> |
    <a href="services.html">Services</a> |
    <a href="trainers.html">Trainers</a> |
    <a href="schedule.html">Schedule</a> |
    <a href="gallery.html">Gallery</a> |
    <a href="membership.html">Membership</a> |
    <a href="testimonials.html">Testimonials</a> |
    <a href="faq.html">FAQ</a> |
    <a href="contact.html">Contact</a>
</nav>

<div id="arrow-left" class="arrow"></div>
<div class="slide slide1"></div>
<div class="slide slide2"></div>
<div class="slide slide3"></div>
<div id="arrow-right" class="arrow"></div>

<?php if ($logged_in): ?>
<div class="active-users">
    <h2>Currently Active Members</h2>
    <ul>
        <?php
        $result = $conn->query("SELECT name FROM active_login");
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($row['name']) . "</li>";
        }
        ?>
    </ul>
</div>
<?php endif; ?>

<footer class="footer">
    <div class="footer-container">
      <p>&copy; 2025 FitZone Gym. All rights reserved.</p>
      <p>
        <a href="privacy.html">Privacy Policy</a> |
        <a href="terms.html">Terms & Conditions</a>
      </p>
      <p>Follow us on:
        <a href="#">Instagram</a> |
        <a href="#">Facebook</a> |
        <a href="#">YouTube</a>
      </p>
    </div>
</footer>

<div id="popup" class="popup-overlay">
    <div class="popup-box">
        <span id="closePopup" class="popup-close">&times;</span>
        <h2>Special Offer!</h2>
        <p>Get <strong>20% OFF</strong> on your annual membership this week only.</p>
        <a href="membership.html" class="btn">Claim Now</a>
    </div>
</div>

<script src="style.js"></script>
<script src="cookie.js"></script>
</body>
</html>
