<?php
session_start();
include_once "./config.php";

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if (!empty($email) && !empty($password)) {
    // Check if credentials match
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}' AND password = '{$password}'");

    if (mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);

        // Set session
        $_SESSION['unique_id'] = $row['unique_id'];

        // Set user status to "Active now"
        $status = "Active now";
        mysqli_query($conn, "UPDATE users SET status = '{$status}' WHERE unique_id = {$row['unique_id']}");

        echo "success";
    } else {
        echo "Email or Password is incorrect!";
    }
} else {
    echo "All input fields are required";
}
