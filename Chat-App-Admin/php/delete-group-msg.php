<?php
session_start();
if (isset($_SESSION['unique_id']) && isset($_POST['message_id'])) {
    include_once "./config.php";

    $message_id = mysqli_real_escape_string($conn, $_POST['message_id']);
    $sender_id = $_SESSION['unique_id'];

    // Ensure the user deleting the message is the sender
    $sql = "DELETE FROM group_messages WHERE message_id = '$message_id' AND sender_id = '$sender_id'";
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn);
    }
} else {
    echo "unauthorized";
}
?>

