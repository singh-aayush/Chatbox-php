<?php
// admin-edit-group.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $group_id = sanitizeInput($conn, $_POST['id']);
    $group_name = sanitizeInput($conn, $_POST['group_name']);
    $new_member_ids = $_POST['new_member_ids'] ?? [];
    $remove_image = isset($_POST['remove_image']);

    // Get current group data
    $stmt = $conn->prepare("SELECT group_image FROM groups WHERE group_id = ?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $group = $stmt->get_result()->fetch_assoc();

    $group_image = $group['group_image'];
    
    // Handle image removal
    if ($remove_image && $group_image !== 'team.png') {
        unlink(__DIR__ . "/images/" . $group_image);
        $group_image = "team.png";
    }
    
    // Handle new image upload
    if (isset($_FILES['group_image'])) {
        $upload = handleFileUpload($_FILES['group_image'], __DIR__ . "/images/");
        if ($upload) {
            if ($group_image !== 'team.png') {
                unlink(__DIR__ . "/images/" . $group_image);
            }
            $group_image = $upload;
        }
    }

    // Update group
    $stmt = $conn->prepare("UPDATE groups SET group_name = ?, group_image = ? WHERE group_id = ?");
    $stmt->bind_param("ssi", $group_name, $group_image, $group_id);
    
    if ($stmt->execute()) {
        // Add new members
        $member_stmt = $conn->prepare("INSERT IGNORE INTO group_members (group_id, unique_id, is_admin) VALUES (?, ?, 0)");
        foreach (array_unique($new_member_ids) as $user_id) {
            $member_stmt->bind_param("ii", $group_id, $user_id);
            $member_stmt->execute();
        }
        
        header("Location: ../public/admin-groups.php?success=Group+updated+successfully");
    } else {
        header("Location: ../public/admin-edit-group.php?id=$group_id&error=Update+failed");
    }
    exit;
}

// If GET request, redirect to view
if (isset($_GET['id'])) {
    header("Location: ../public/admin-edit-group.php?id=" . sanitizeInput($conn, $_GET['id']));
} else {
    header("Location: ../public/admin-groups.php");
}
?>