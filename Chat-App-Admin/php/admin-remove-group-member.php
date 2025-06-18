<?php
// admin-remove-group-member.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

if (isset($_POST['group_id']) && isset($_POST['user_id'])) {
    $group_id = sanitizeInput($conn, $_POST['group_id']);
    $user_id = sanitizeInput($conn, $_POST['user_id']);
    
    $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ? AND unique_id = ?");
    $stmt->bind_param("ii", $group_id, $user_id);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    exit;
}

echo "error";
?>