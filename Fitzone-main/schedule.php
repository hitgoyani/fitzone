<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    include 'db.php';
}

function log_activity($message) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents('activity_log.txt', "[$timestamp] $message\n", FILE_APPEND);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = isset($_SESSION['user']) ? $_SESSION['user'] : (isset($_POST['user']) ? htmlspecialchars($_POST['user']) : 'Guest');
    if (isset($_POST['schedule']) && is_array($_POST['schedule'])) {
        $selected = $_POST['schedule'];
        $data = "User: $user | Schedule: " . implode(", ", $selected) . "\n";
        file_put_contents("schedules.txt", $data, FILE_APPEND);
        log_activity("SUCCESS: $user saved schedule: " . implode(", ", $selected));
        echo "<h2 style='color:green;text-align:center;'>Schedule Saved!</h2>";
    echo "<p style='text-align:center;'><a href='index.php'>Back to Home</a></p>";
    } else {
        log_activity("ERROR: $user tried to submit schedule but no sessions selected.");
        echo "<h2 style='color:red;text-align:center;'>No sessions selected.</h2>";
        echo "<p style='text-align:center;'><a href='schedule.html'>Try Again</a></p>";
    }
} else {
    log_activity("INVALID REQUEST: schedule.php accessed with method {$_SERVER['REQUEST_METHOD']}.");
    echo "<h2 style='color:red;text-align:center;'>Invalid Request.</h2>";
    echo "<p style='text-align:center;'><a href='schedule.html'>Back</a></p>";
}
?>
