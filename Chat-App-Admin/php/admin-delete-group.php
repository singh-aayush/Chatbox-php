<?php
// admin-delete-group.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

if (isset($_POST['id'])) {
    $group_id = sanitizeInput($conn, $_POST['id']);
    
    // Get group info first
    $stmt = $conn->prepare("SELECT group_image FROM groups WHERE group_id = ?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $group = $stmt->get_result()->fetch_assoc();
    
    if ($group) {
        // Delete image if not default
        if ($group['group_image'] !== 'team.png') {
            unlink(__DIR__ . "/../images/" . $group['group_image']);
        }
        
        // Delete messages, members, then group
        $conn->query("DELETE FROM group_messages WHERE group_id = $group_id");
        $conn->query("DELETE FROM group_members WHERE group_id = $group_id");
        $conn->query("DELETE FROM groups WHERE group_id = $group_id");
    }
    
    header("Location: ../public/admin-groups.php?success=Group+deleted");
    exit;
}

header("Location: ../public/admin-groups.php");
?>