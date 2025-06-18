<?php
session_start();
include_once "./config.php";

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if (!empty($email) && !empty($password)) {
    // Check if email ends with @gmail.com
    if (!preg_match("/@gmail\.com$/i", $email)) {
        echo "Only Gmail addresses are allowed (@gmail.com)";
        exit();
    }
    
    // Fetch user with matching email
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");

    if (mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);

        // Verify hashed password
        if (password_verify($password, $row['password'])) {
            // Set session
            $_SESSION['unique_id'] = $row['unique_id'];

            // Update status
            $status = "Active now";
            mysqli_query($conn, "UPDATE users SET status = '{$status}' WHERE unique_id = {$row['unique_id']}");

            echo "success";
        } else {
            echo "Incorrect email or password!";
        }
    } else {
        echo "Incorrect email or password!";
    }
} else {
    echo "All input fields are required";
}
?>