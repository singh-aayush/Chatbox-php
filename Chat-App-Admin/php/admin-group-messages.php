<?php
// admin-group-messages.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

if (isset($_GET['id'])) {
    $group_id = sanitizeInput($conn, $_GET['id']);
    header("Location: ../public/admin-group-messages.php?id=$group_id");
    exit;
}

header("Location: ../public/admin-groups.php");
?>