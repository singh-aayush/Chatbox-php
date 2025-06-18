<?php
session_start();
error_log("Starting group chat file/message processing");

if (!isset($_SESSION['unique_id'])) {
    header("Location: ../login.php");
    exit;
}

include_once "./config.php";

$group_id  = intval($_POST['group_id']);     // group_id expected to be numeric
$sender_id = intval($_POST['sender_id']);    // sender_id expected to be numeric
$message   = mysqli_real_escape_string($conn, $_POST['message'] ?? '');

$file_path = '';

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB

    $file_type = $_FILES['file']['type'];
    $file_size = $_FILES['file']['size'];

    if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
        $upload_dir = '../php/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $original_name = basename($_FILES['file']['name']);
        $extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid('grpfile_', true) . '.' . $extension;
        $destination = $upload_dir . $new_file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            $file_path = $destination;
            error_log("File uploaded: $file_path");
        } else {
            error_log(" File move failed.");
        }
    } else {
        error_log(" Invalid file type or size too large.");
    }
}

// Insert message if at least text or file is present
if (!empty($message) || !empty($file_path)) {
    $stmt = $conn->prepare("INSERT INTO group_messages (group_id, sender_id, message, file_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $group_id, $sender_id, $message, $file_path);
    
    if ($stmt->execute()) {
        error_log(" Group message inserted.");
    } else {
        error_log(" DB insert error: " . $stmt->error);
    }

    $stmt->close();
} else {
    error_log(" No message or file provided.");
}
?>
