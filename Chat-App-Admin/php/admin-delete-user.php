<?php
// admin-delete-user.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

if (isset($_POST['id'])) {
    $user_id = sanitizeInput($conn, $_POST['id']);
    
    // Get user info first
    $stmt = $conn->prepare("SELECT img FROM users WHERE unique_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if ($user) {
        // Delete image if exists
        if (!empty($user['img'])) {
            unlink(__DIR__ . "/../images/" . $user['img']);
        }
        
        // Delete user
        $conn->query("DELETE FROM users WHERE unique_id = $user_id");
    }
    
    header("Location: ../public/admin-users.php?success=User+deleted");
    exit;
}

header("Location: ../public/admin-users.php");
?>