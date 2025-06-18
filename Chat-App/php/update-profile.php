<?php
session_start();
include_once "config.php";

// Check if user is logged in
if (!isset($_SESSION['unique_id'])) {
    header("Location: login.php?error=Unauthorized access");
    exit;
}

// Verify database connection
if (!$conn) {
    header("Location: ../edit_profile.php?error=Database connection failed");
    exit;
}

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../edit_profile.php?error=Invalid request method");
    exit;
}

// Get and validate unique_id
$unique_id = $_SESSION['unique_id'];
// If unique_id is sent via POST, verify it matches session
if (isset($_POST['unique_id']) && $_POST['unique_id'] !== $unique_id) {
    header("Location: ../edit_profile.php?error=Unauthorized user ID");
    exit;
}

// Sanitize input data
$fname = mysqli_real_escape_string($conn, $_POST['fname'] ?? '');
$lname = mysqli_real_escape_string($conn, $_POST['lname'] ?? '');
$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$designation = mysqli_real_escape_string($conn, $_POST['designation'] ?? '');
$location = mysqli_real_escape_string($conn, $_POST['location'] ?? '');
$employee_code = mysqli_real_escape_string($conn, $_POST['employee_code'] ?? '');
$password = !empty($_POST['password']) ? $_POST['password'] : null;

// Validate required fields
if (empty($fname) || empty($lname)) {
    header("Location: ../edit_profile.php?error=First name and Last name are required");
    exit;
}

// Email format validation (if email is editable; skip if readonly in form)
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../edit_profile.php?error=Invalid email format");
    exit;
}

// Check if email is unique (optional, only if email is editable)
if (!empty($email)) {
    $email_check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND unique_id != '$unique_id'");
    if (mysqli_num_rows($email_check) > 0) {
        header("Location: ../edit_profile.php?error=Email is already in use");
        exit;
    }
}

// Handle image upload
$update_img = "";
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $img_name = $_FILES['image']['name'];
    $img_tmp = $_FILES['image']['tmp_name'];
    $img_size = $_FILES['image']['size'];
    $img_explode = explode('.', $img_name);
    $img_ext = strtolower(end($img_explode));
    $valid_extensions = ['jpg', 'jpeg', 'png'];
    $max_file_size = 5 * 1024 * 1024; // 5MB

    // Validate image
    if (!in_array($img_ext, $valid_extensions)) {
        header("Location: ../edit_profile.php?error=Invalid image format. Only JPG, JPEG, PNG allowed");
        exit;
    }
    if ($img_size > $max_file_size) {
        header("Location: ../edit_profile.php?error=Image size exceeds 5MB limit");
        exit;
    }

    // Ensure images directory exists and is writable
    $upload_dir = "images/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    if (!is_writable($upload_dir)) {
        header("Location: ../edit_profile.php?error=Image upload directory is not writable");
        exit;
    }

    $new_img_name = time() . '_' . $img_name;
    $upload_path = $upload_dir . $new_img_name;
    if (!move_uploaded_file($img_tmp, $upload_path)) {
        header("Location: ../edit_profile.php?error=Failed to upload image");
        exit;
    }
    $update_img = ", img = '$new_img_name'";
}

// Handle password update
$update_password = "";
if (!empty($password)) {
    // Basic password validation (e.g., minimum length)
    if (strlen($password) < 6) {
        header("Location: ../edit_profile.php?error=Password must be at least 6 characters");
        exit;
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $update_password = ", password = '$hashed_password'";
}

// Build and execute update query
$sql = "UPDATE users 
        SET fname = '$fname', 
            lname = '$lname', 
            designation = '$designation', 
            location = '$location', 
            employee_code = '$employee_code' 
            $update_img 
            $update_password 
        WHERE unique_id = '$unique_id'";

if (mysqli_query($conn, $sql)) {
    header("Location: ../public/edit_profile.php?success=Profile updated successfully");
    exit;
} else {
    $error = mysqli_error($conn);
    error_log("Profile update failed: $error");
    header("Location: ../edit_profile.php?error=Update failed: " . urlencode($error));
    exit;
}
?>