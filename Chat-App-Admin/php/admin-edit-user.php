[file name]: admin-edit-user.php
[file content begin]
<?php
// admin-edit-user.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = sanitizeInput($conn, $_POST['id']);
    
    // Get current user data
    $stmt = $conn->prepare("SELECT img FROM users WHERE unique_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (!$user) {
        header("Location: ../public/admin-users.php?error=User+not+found");
        exit;
    }

    $img_name = $user['img'];
    
    // Handle image upload
    if (isset($_FILES['image'])) {
        $upload = handleFileUpload($_FILES['image'], __DIR__ . "/images/");
        if ($upload) {
            // Delete old image if exists
            if (!empty($img_name)) {
                unlink(__DIR__ . "/images/" . $img_name);
            }
            $img_name = $upload;
        }
    }
    
    // Handle image removal
    if (isset($_POST['remove_image']) && !empty($img_name)) {
        unlink(__DIR__ . "/images/" . $img_name);
        $img_name = "";
    }

    // Build update query
    $fields = [
        'fname' => sanitizeInput($conn, $_POST['fname']),
        'lname' => sanitizeInput($conn, $_POST['lname']),
        'designation' => sanitizeInput($conn, $_POST['designation']),
        'location' => sanitizeInput($conn, $_POST['location']),
        'employee_code' => sanitizeInput($conn, $_POST['employee_code']),
        'role' => sanitizeInput($conn, $_POST['role']),
        'img' => $img_name
    ];
    
    // Add password if provided (and hash it)
    if (!empty($_POST['new_password'])) {
        $fields['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    }

    // Build SQL dynamically
    $sql = "UPDATE users SET ";
    $types = "";
    $values = [];
    
    foreach ($fields as $field => $value) {
        $sql .= "$field = ?, ";
        $types .= is_int($value) ? "i" : "s";
        $values[] = $value;
    }
    
    $sql = rtrim($sql, ", ") . " WHERE unique_id = ?";
    $types .= "i";
    $values[] = $user_id;
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        header("Location: ../public/admin-users.php?success=User+updated+successfully");
    } else {
        header("Location: ../public/admin-edit-user.php?id=$user_id&error=Update+failed");
    }
    exit;
}

// If GET request, redirect to view
if (isset($_GET['id'])) {
    header("Location: ../public/admin-edit-user.php?id=" . sanitizeInput($conn, $_GET['id']));
} else {
    header("Location: ../public/admin-users.php");
}
?>
[file content end]