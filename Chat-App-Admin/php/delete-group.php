<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";

    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
    $user_id = $_SESSION['unique_id'];

    // Verify the user is the group creator
    $check_creator = mysqli_query($conn, "SELECT * FROM groups WHERE group_id = '$group_id' AND created_by = '$user_id'");
    if (mysqli_num_rows($check_creator) > 0) {
        // Delete group image if exists
        $group = mysqli_fetch_assoc($check_creator);
        if (!empty($group['group_image']) && file_exists("images/" . $group['group_image'])) {
            unlink("images/" . $group['group_image']);
        }

        // Delete group messages
        mysqli_query($conn, "DELETE FROM group_messages WHERE group_id = '$group_id'");

        // Delete group members
        mysqli_query($conn, "DELETE FROM group_members WHERE group_id = '$group_id'");

        // Delete the group
        mysqli_query($conn, "DELETE FROM groups WHERE group_id = '$group_id'");

        header("Location: ../public/users.php");
        exit();
    } else {
        echo "You don't have permission to delete this group.";
    }
} else {
    header("location: ../login.php");
}
?>