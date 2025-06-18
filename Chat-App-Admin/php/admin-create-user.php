<?php
// admin-create-user.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
checkAdminAuth();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $required = ['fname', 'lname', 'email', 'password'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            header("Location: ../public/admin-create-user.php?error=" . urlencode("All fields are required"));
            exit;
        }
    }

    $email = sanitizeInput($conn, $_POST['email']);
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT unique_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        header("Location: ../public/admin-create-user.php?error=" . urlencode("Email already exists"));
        exit;
    }

    // Handle file upload
    $img_name = "default.jpg"; // Default image if none is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = handleFileUpload($_FILES['image'], __DIR__ . "/images/");
        if ($upload) {
            $img_name = $upload;
        } else {
            header("Location: ../public/admin-create-user.php?error=" . urlencode("Failed to upload image"));
            exit;
        }
    }

    // Hash the password before storing
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert user
    $random_id = rand(time(), 10000000);
    $stmt = $conn->prepare("INSERT INTO users (unique_id, fname, lname, email, password, img, status, designation, location, employee_code, role) 
                           VALUES (?, ?, ?, ?, ?, ?, 'Offline now', ?, ?, ?, ?)");
    
    $stmt->bind_param("isssssssss", 
        $random_id,
        sanitizeInput($conn, $_POST['fname']),
        sanitizeInput($conn, $_POST['lname']),
        $email,
        $hashed_password, // Use the hashed password here
        $img_name,
        sanitizeInput($conn, $_POST['designation']),
        sanitizeInput($conn, $_POST['location']),
        sanitizeInput($conn, $_POST['employee_code']),
        sanitizeInput($conn, $_POST['role'])
    );
    
    if ($stmt->execute()) {
        header("Location: ../public/admin-users.php?success=User+created+successfully");
    } else {
        header("Location: ../public/admin-create-user.php?error=" . urlencode("Failed to create user"));
    }
    exit;
}

header("Location: ../public/admin-create-user.php");
?>
