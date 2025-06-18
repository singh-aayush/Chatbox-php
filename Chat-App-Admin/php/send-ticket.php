<?php
session_start();
include_once "config.php";

if (isset($_POST['subject'], $_POST['message'])) {
    $user_id = $_SESSION['unique_id'];
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $attachment_path = "";
    if (!empty($_FILES['attachment']['name'])) {
        $file_name = basename($_FILES['attachment']['name']);
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file = uniqid("TICKET_", true) . '.' . $file_ext;
        $upload_dir = "tickets/";
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        $attachment_path = $upload_dir . $new_file;
        move_uploaded_file($file_tmp, $attachment_path);
    }

    $query = "INSERT INTO support_tickets (user_id, subject, message, attachment) 
              VALUES ('$user_id', '$subject', '$message', '$attachment_path')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Ticket submitted successfully!'); window.location.href = '../public/settings.php';</script>";
    } else {
        echo "Error submitting ticket.";
    }
} else {
    echo "Subject and message are required.";
}
?>
