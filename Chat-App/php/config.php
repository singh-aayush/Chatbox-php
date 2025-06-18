<?php
$conn = mysqli_connect("localhost", "root", "root@89", "php-chatapp");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
