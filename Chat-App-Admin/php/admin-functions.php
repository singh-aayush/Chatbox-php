<?php
// admin-functions.php
function checkAdminAuth() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['admin_unique_id'])) {
        header("Location: ../public/admin-login.php");
        exit;
    }
}

function sanitizeInput($conn, $data) {
    return $conn->real_escape_string(trim($data));
}

function handleFileUpload($file, $targetDir) {
    // Log file details for debugging
    error_log("File upload attempt: " . print_r($file, true));

    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("File upload error code: " . $file['error']);
        return false;
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        error_log("Invalid file type: " . $fileType);
        return false;
    }

    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $maxSize) {
        error_log("File too large: " . $file['size'] . " bytes");
        return false;
    }

    // Ensure target directory exists and is writable
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            error_log("Failed to create directory: " . $targetDir);
            return false;
        }
    }
    if (!is_writable($targetDir)) {
        error_log("Directory not writable: " . $targetDir);
        return false;
    }

    // Generate unique file name
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueName = uniqid('profile_', true) . '.' . $fileExtension;
    $targetPath = $targetDir . $uniqueName;

    // Move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        error_log("File uploaded successfully: " . $targetPath);
        return $uniqueName; // Return the file name
    } else {
        error_log("Failed to move file to: " . $targetPath);
        return false;
    }
}
?>