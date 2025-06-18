<?php
// admin-login.php
require_once "C:/xampp/htdocs/Chat-App/php/config.php";
require_once __DIR__ . '/../php/admin-functions.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo "All fields are required";
        exit;
    }

    $stmt = $conn->prepare("SELECT unique_id, password FROM users WHERE email = ? AND role = 'Admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) { // Verify hashed password
            session_start();
            $_SESSION['admin_unique_id'] = $user['unique_id'];
            echo "success";
        } else {
            echo "Invalid credentials";
        }
    } else {
        echo "Invalid credentials";
    }
    exit;
}

header("Location: ../public/admin-login.php");
?>
