<?php
session_start();
include("db.php"); // database connection

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM active_logins WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }


    session_unset();
    session_destroy();
}


header("Location: login.html?logout=1");
exit();
?>
