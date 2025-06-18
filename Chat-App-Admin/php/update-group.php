<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";

    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
    $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
    $members = $_POST['members'] ?? [];
    $created_by = $_SESSION['unique_id'];

    // Verify the user is the group creator
    $check_creator = mysqli_query($conn, "SELECT * FROM groups WHERE group_id = '$group_id' AND created_by = '$created_by'");
    if (mysqli_num_rows($check_creator) > 0) {
        // Handle image upload
        $image_name = '';
        if (isset($_FILES['group_image']) && $_FILES['group_image']['error'] === UPLOAD_ERR_OK) {
            $img_name = $_FILES['group_image']['name'];
            $img_tmp = $_FILES['group_image']['tmp_name'];
            $img_ext = pathinfo($img_name, PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png'];

            if (in_array(strtolower($img_ext), $allowed)) {
                $new_img_name = time() . "_" . uniqid() . "." . $img_ext;
                $upload_path = "images/" . $new_img_name;

                if (move_uploaded_file($img_tmp, $upload_path)) {
                    // Delete old image if it exists
                    $old_img = mysqli_fetch_assoc(mysqli_query($conn, "SELECT group_image FROM groups WHERE group_id = '$group_id'"));
                    if (!empty($old_img['group_image']) && file_exists("images/" . $old_img['group_image'])) {
                        unlink("images/" . $old_img['group_image']);
                    }
                    $image_name = $new_img_name;
                }
            }
        }

        // Update group info
        $update_query = "UPDATE groups SET group_name = '$group_name'";
        if (!empty($image_name)) {
            $update_query .= ", group_image = '$image_name'";
        }
        $update_query .= " WHERE group_id = '$group_id'";

        if (mysqli_query($conn, $update_query)) {
            // Update members
            // First, remove all current members except creator
            mysqli_query($conn, "DELETE FROM group_members WHERE group_id = '$group_id' AND unique_id != '$created_by'");

            // Add selected members back
            foreach ($members as $member_id) {
                $member_id = mysqli_real_escape_string($conn, $member_id);
                mysqli_query($conn, "INSERT INTO group_members (group_id, unique_id) VALUES ('$group_id', '$member_id')");
            }

            // Ensure creator is always an admin
            mysqli_query($conn, "UPDATE group_members SET is_admin = true WHERE group_id = '$group_id' AND unique_id = '$created_by'");

            header("Location: ../public/group-chat.php?group_id=$group_id");
            exit();
        } else {
            echo "Failed to update group: " . mysqli_error($conn);
        }
    } else {
        echo "You don't have permission to edit this group.";
    }
} else {
    header("location: ../login.php");
}
?>