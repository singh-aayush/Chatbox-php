<?php
session_start();
include_once "C:/xampp/htdocs/Chat-App/php/config.php";

if (!isset($_SESSION['admin_unique_id'])) {
    header("Location: ../public/admin-login.php");
    exit;
}

$sql = mysqli_query($conn, "SELECT g.*, COUNT(gm.unique_id) as member_count FROM groups g LEFT JOIN group_members gm ON g.group_id = gm.group_id GROUP BY g.group_id");
if (!$sql) {
    error_log("Select groups failed: " . mysqli_error($conn));
    $groups = [];
} else {
    $groups = [];
    while ($row = mysqli_fetch_assoc($sql)) {
        $groups[] = $row;
    }
}

$_SESSION['admin_groups'] = $groups;
error_log("Fetched groups: " . print_r($groups, true));
header("Location: ../public/admin-groups.php");
?>