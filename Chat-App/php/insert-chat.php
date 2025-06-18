<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "./config.php";

    $outgoing_id = mysqli_real_escape_string($conn, $_POST['outgoing_id']); // Sender
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']); // Receiver
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Handle file upload
    $file = '';
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (in_array($_FILES['file']['type'], $allowed_types) && $_FILES['file']['size'] <= $max_size) {
            $upload_dir = '../php/uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $file = $upload_dir . uniqid() . '.' . $file_ext;

            if (!move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
                $file = '';
            }
        } else {
            $file = '';
        }
    }

    // Check if approved to chat
    $checkRequest = mysqli_query($conn, "SELECT * FROM message_requests 
        WHERE sender_id = {$outgoing_id} AND receiver_id = {$incoming_id} AND status = 'accepted'");

    if (mysqli_num_rows($checkRequest) === 0) {
        // Check if already requested
        $existingRequest = mysqli_query($conn, "SELECT * FROM message_requests 
            WHERE sender_id = {$outgoing_id} AND receiver_id = {$incoming_id}");

        if (mysqli_num_rows($existingRequest) === 0) {
            // Send request if not already present
            mysqli_query($conn, "INSERT INTO message_requests (sender_id, receiver_id) 
                VALUES ({$outgoing_id}, {$incoming_id})");
        }

        echo "Message not allowed. Waiting for receiver approval.";
        exit;
    }

    // Send message if either text or file exists
    if (!empty($message) || !empty($file)) {
        $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, file)
            VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', '{$file}')") or die(mysqli_error($conn));
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>

