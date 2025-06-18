<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "./config.php";
    $user_id = $_SESSION['unique_id'];

    // Fetch all pending requests where current user is the receiver
    $sql = "SELECT r.*, u.fname, u.lname, u.img FROM message_requests r 
            JOIN users u ON r.sender_id = u.unique_id 
            WHERE r.receiver_id = ? AND r.status = 'pending'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $requests = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $requests[] = $row;
        }
    }

    echo json_encode($requests);
} else {
    echo json_encode(["error" => "Not logged in"]);
}
?>