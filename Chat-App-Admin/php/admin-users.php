<?php
session_start();
include_once "C:/xampp/htdocs/Chat-App/php/config.php";

if (!isset($_SESSION['admin_unique_id'])) {
    header("Location: ../public/admin-login.php");
    exit;
}

$sql = mysqli_query($conn, "SELECT * FROM users ORDER BY fname ASC");
if (!$sql) {
    error_log("Select users failed: " . mysqli_error($conn));
    $users = [];
} else {
    $users = [];
    while ($row = mysqli_fetch_assoc($sql)) {
        $users[] = $row;
    }
}

$_SESSION['admin_users'] = $users;
error_log("Fetched users: " . print_r($users, true));
header("Location: ../public/admin-users.php");
?>