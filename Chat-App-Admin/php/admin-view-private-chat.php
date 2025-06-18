<?php
// admin-view-private-chat.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

if (isset($_GET['user_id1']) && isset($_GET['user_id2'])) {
    $user_id1 = sanitizeInput($conn, $_GET['user_id1']);
    $user_id2 = sanitizeInput($conn, $_GET['user_id2']);
    header("Location: ../public/admin-view-private-chat.php?user_id1=$user_id1&user_id2=$user_id2");
    exit;
}

header("Location: ../public/admin-private-chats.php");
?>