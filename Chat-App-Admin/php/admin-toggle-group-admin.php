<?php
// admin-toggle-group-admin.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

if (isset($_POST['group_id']) && isset($_POST['user_id'])) {
    $group_id = sanitizeInput($conn, $_POST['group_id']);
    $user_id = sanitizeInput($conn, $_POST['user_id']);
    
    // Get current status
    $stmt = $conn->prepare("SELECT is_admin FROM group_members WHERE group_id = ? AND unique_id = ?");
    $stmt->bind_param("ii", $group_id, $user_id);
    $stmt->execute();
    $member = $stmt->get_result()->fetch_assoc();
    
    if ($member) {
        $new_status = $member['is_admin'] ? 0 : 1;
        
        // Update status
        $update_stmt = $conn->prepare("UPDATE group_members SET is_admin = ? WHERE group_id = ? AND unique_id = ?");
        $update_stmt->bind_param("iii", $new_status, $group_id, $user_id);
        
        if ($update_stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
    exit;
}

echo "error";
?>