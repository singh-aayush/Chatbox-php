<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "./config.php";
    $user_id = $_SESSION['unique_id'];

    // Fetch all pending requests where current user is the receiver
    $sql = mysqli_query($conn, "SELECT r.*, u.fname, u.lname, u.img FROM message_requests r
        JOIN users u ON r.sender_id = u.unique_id
        WHERE r.receiver_id = {$user_id} AND r.status = 'pending'");

    $requests = [];

    if (mysqli_num_rows($sql) > 0) {
        while ($row = mysqli_fetch_assoc($sql)) {
            $requests[] = $row;
        }
    }

    echo json_encode($requests);
} else {
    echo json_encode(["error" => "Not logged in"]);
}
?>
