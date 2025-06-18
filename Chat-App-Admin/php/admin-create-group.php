<?php
// admin-create-group.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $group_name = sanitizeInput($conn, $_POST['group_name']);
    $user_ids = $_POST['member_ids'] ?? [];
    
    $group_image = "team.png";
    if (isset($_FILES['group_image'])) {
        $upload = handleFileUpload($_FILES['group_image'], __DIR__ . "/images/");
        if ($upload) $group_image = $upload;
    }

    $stmt = $conn->prepare("INSERT INTO groups (group_name, created_by, group_image) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $group_name, $_SESSION['admin_unique_id'], $group_image);
    
    if ($stmt->execute()) {
        $group_id = $conn->insert_id;
        
        // Add members
        $member_stmt = $conn->prepare("INSERT INTO group_members (group_id, unique_id, is_admin) VALUES (?, ?, 0)");
        foreach (array_unique($user_ids) as $user_id) {
            $member_stmt->bind_param("ii", $group_id, $user_id);
            $member_stmt->execute();
        }
        
        header("Location: ../public/admin-groups.php?success=Group+created+successfully");
    } else {
        header("Location: ../public/admin-create-group.php?error=Failed+to+create+group");
    }
    exit;
}

// If not POST, redirect to form
header("Location: ../public/admin-create-group.php");
?>