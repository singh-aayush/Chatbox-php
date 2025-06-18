<?php
// admin-dashboard.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

// Dashboard doesn't need processing, just redirect
header("Location: ../public/admin-dashboard.php");
?>