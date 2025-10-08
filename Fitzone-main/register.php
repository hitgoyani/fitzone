<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $age = intval($_POST['age']);
    $mobileno = htmlspecialchars(trim($_POST['mobileno']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    
    function is_valid_password($pwd, &$err) {
        $err = [];
        if (strlen($pwd) < 8) $err[] = "Password must be at least 8 characters.";
        if (!preg_match('/[A-Z]/', $pwd)) $err[] = "Password must include at least one uppercase letter.";
        if (!preg_match('/[a-z]/', $pwd)) $err[] = "Password must include at least one lowercase letter.";
        if (!preg_match('/[0-9]/', $pwd)) $err[] = "Password must include at least one digit.";
        if (!preg_match('/[\W_]/', $pwd)) $err[] = "Password must include at least one special character.";
        return empty($err);
    }

  
    if ($password !== $confirm) {
        die("❌ Passwords do not match.");
    }

    $pwErrors = [];
    if (!is_valid_password($password, $pwErrors)) {
        die("❌ " . implode(" ", $pwErrors));
    }

 
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO members (name, age, mobileno, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $name, $age, $mobileno, $email, $hash);

    if ($stmt->execute()) {
      
        $submittedAt = date("Y-m-d H:i:s");
        $line = "Name: $name | Age: $age | Mobile: $mobileno | Email: $email | Password: $hash | Time: $submittedAt" . PHP_EOL;
        file_put_contents("members.txt", $line, FILE_APPEND | LOCK_EX);

        $_SESSION['user'] = $name;
        header("Location: login.html?registered=1");
        exit;
    } else {
        die("❌ Database error: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    die("❌ Invalid Request");
}
?>
