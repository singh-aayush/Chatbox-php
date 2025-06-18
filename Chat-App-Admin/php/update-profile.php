<?php
session_start();
include_once "./config.php";

if (!isset($_SESSION['unique_id'])) {
    echo "Unauthorized access";
    exit;
}

$id = $_SESSION['unique_id'];
$fname = mysqli_real_escape_string($conn, $_POST['fname']);
$lname = mysqli_real_escape_string($conn, $_POST['lname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$designation = mysqli_real_escape_string($conn, $_POST['designation']);
$location = mysqli_real_escape_string($conn, $_POST['location']);
$employee_code = mysqli_real_escape_string($conn, $_POST['employee_code']);
$password = !empty($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : null;

// Validate required fields
if (empty($fname) || empty($lname) || empty($email)) {
    echo "First name, Last name, and Email are required.";
    exit;
}

// Email format validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit;
}

// Optional image update
$update_img = "";
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $img_name = $_FILES['image']['name'];
    $img_tmp = $_FILES['image']['tmp_name'];
    $img_explode = explode('.', $img_name);
    $img_ext = strtolower(end($img_explode));
    $valid_extensions = ['jpg', 'jpeg', 'png'];

    if (in_array($img_ext, $valid_extensions)) {
        $new_img_name = time() . $img_name;
        if (move_uploaded_file($img_tmp, "images/" . $new_img_name)) {
            $update_img = ", img = '{$new_img_name}'";
        } else {
            echo "Failed to upload image.";
            exit;
        }
    } else {
        echo "Invalid image format. Only JPG, JPEG, PNG are allowed.";
        exit;
    }
}

// Optional password update
$update_password = "";
if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $update_password = ", password = '{$hashed_password}'";
}

$sql = "UPDATE users 
        SET fname = '{$fname}', 
            lname = '{$lname}', 
            email = '{$email}', 
            designation = '{$designation}', 
            location = '{$location}', 
            employee_code = '{$employee_code}' 
            {$update_img} 
            {$update_password}
        WHERE unique_id = '{$id}'";

if (mysqli_query($conn, $sql)) {
    echo "success";
} else {
    echo "Update failed: " . mysqli_error($conn);
}
?>

