<?php
$conn = mysqli_connect("localhost", "root", "", "php-chatapp");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
