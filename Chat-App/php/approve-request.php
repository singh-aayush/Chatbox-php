<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "./config.php";

    $receiver_id = $_SESSION['unique_id'];
    $sender_id = mysqli_real_escape_string($conn, $_POST['sender_id']);
    $action = mysqli_real_escape_string($conn, $_POST['action']); // "accept" or "reject"

    if ($action === "accept") {
        //  Update original request to accepted
        $query1 = "UPDATE message_requests 
                   SET status = 'accepted' 
                   WHERE sender_id = {$sender_id} AND receiver_id = {$receiver_id}";

        //  Insert or update reverse request as accepted (mutual)
        $checkReverse = mysqli_query($conn, "
            SELECT * FROM message_requests 
            WHERE sender_id = {$receiver_id} AND receiver_id = {$sender_id}
        ");

        if (mysqli_num_rows($checkReverse) > 0) {
            $query2 = "UPDATE message_requests 
                       SET status = 'accepted' 
                       WHERE sender_id = {$receiver_id} AND receiver_id = {$sender_id}";
        } else {
            $query2 = "INSERT INTO message_requests (sender_id, receiver_id, status)
                       VALUES ({$receiver_id}, {$sender_id}, 'accepted')";
        }

        // Run both queries
        mysqli_query($conn, $query1);
        mysqli_query($conn, $query2);

        echo json_encode(["status" => "success"]);
    }
    //  else if ($action === "reject") {
    //     //  Reject just deletes the request
    //     $query = "DELETE FROM message_requests 
    //               WHERE sender_id = {$sender_id} AND receiver_id = {$receiver_id}";
    //     mysqli_query($conn, $query);
    //     echo json_encode(["status" => "rejected"]);
    // } else {
    //     echo json_encode(["status" => "invalid action"]);
    // }
} else {
    echo json_encode(["status" => "unauthorized"]);
}
