<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "./config.php";

    $msg_id = intval($_POST['msg_id']);
    $user_id = $_SESSION['unique_id'];

    // Check ownership
    $sql = mysqli_query($conn, "SELECT * FROM messages WHERE msg_id = $msg_id AND outgoing_msg_id = $user_id");
    if (mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);

        // Delete file if attached
        if (!empty($row['file']) && file_exists($row['file'])) {
            unlink($row['file']);
        }

        // Delete message
        mysqli_query($conn, "DELETE FROM messages WHERE msg_id = $msg_id");
        echo "Deleted successfully";
    } else {
        echo "You can only delete your own messages.";
    }
} else {
    echo "Unauthorized request.";
}
?>
