<?php
// admin-private-chats.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

header("Location: ../public/admin-private-chats.php");
?>