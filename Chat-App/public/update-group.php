<?php
session_start();
require_once '../php/config.php';

if (isset($_POST['update_group'])) {
    $group_id = $_POST['group_id'];
    $user_id = $_SESSION['unique_id'];
    $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
    $members = $_POST['members']; // array

    //Update group name
    $query = "UPDATE groups SET group_name = '$group_name' WHERE group_id = $group_id AND created_by = $user_id";
    mysqli_query($conn, $query);

    //Remove all existing members (except admin)
    mysqli_query($conn, "DELETE FROM group_members WHERE group_id = $group_id AND is_admin = 0");

    //Reinsert selected members
    foreach ($members as $member_id) {
        // Skip if admin (already present)
        if ($member_id == $user_id) continue;

        $check = mysqli_query($conn, "SELECT * FROM group_members WHERE group_id = $group_id AND unique_id = $member_id");
        if (mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "INSERT INTO group_members (group_id, unique_id, is_admin) VALUES ($group_id, $member_id, 0)");
        }
    }

    header("Location: group-chat.php?group_id=$group_id");
    exit();
}
?>
