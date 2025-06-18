<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "./config.php";
    $logout_id = mysqli_real_escape_string($conn, $_GET['logout_id']);

    if (isset($logout_id)) {
        $status = "Offline now";
        //Update only the current user
        $sql = mysqli_query($conn, "UPDATE users SET status = '{$status}' WHERE unique_id = '{$logout_id}'");

        if ($sql) {
            session_unset();
            session_destroy();
            header("location: ../public/login.php");
        }
    } else {
        header("location: ../public/users.php");
    }
} else {
    header("location: ../public/login.php");
}

?>