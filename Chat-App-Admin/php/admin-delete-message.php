<?php
// admin-delete-message.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

if (isset($_POST['id'])) {
    $message_id = sanitizeInput($conn, $_POST['id']);
    
    // Get message info first
    $stmt = $conn->prepare("SELECT file_path FROM group_messages WHERE message_id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $message = $stmt->get_result()->fetch_assoc();
    
    if ($message) {
        // Delete attachment if exists
        if (!empty($message['file_path'])) {
            unlink(__DIR__ . "/../" . $message['file_path']);
        }
        
        // Delete message
        $conn->query("DELETE FROM group_messages WHERE message_id = $message_id");
        echo "success";
    } else {
        echo "error";
    }
    exit;
}

echo "error";
?>