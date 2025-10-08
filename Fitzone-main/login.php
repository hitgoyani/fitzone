<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // fetch a single user row for this email. If duplicates exist, prefer an admin row.
    // NOTE: duplicate emails in the DB will cause the original code to return >1 rows
    // and fail. The real fix is to clean duplicates and add a UNIQUE index on email.
    $stmt = $conn->prepare("SELECT id, name, password, role FROM members WHERE email = ? ORDER BY (role='admin') DESC, id ASC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;

    if ($row) {
        if (password_verify($password, $row['password'])) {
            // ✅ use user_id and cache user role in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['role'] = $row['role'] ?? '';

            // redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "❌ Invalid password.";
        }
    } else {
        echo "❌ User not found.";
    }
}
?>
