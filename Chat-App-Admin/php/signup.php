<?php
session_start();
include_once "./config.php";

$fname = mysqli_real_escape_string($conn, $_POST['fname']);
$lname = mysqli_real_escape_string($conn, $_POST['lname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
$designation = mysqli_real_escape_string($conn, $_POST['designation']);
$location = mysqli_real_escape_string($conn, $_POST['location']);
$employee_code = mysqli_real_escape_string($conn, $_POST['employee_code']);

if (!empty($fname) && !empty($lname) && !empty($email) && !empty($password)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email ends with @gmail.com
        if (!preg_match("/@gmail\.com$/i", $email)) {
            echo "Only Gmail addresses are allowed (@gmail.com)";
            exit();
        }
        
        $sql = mysqli_query($conn, "SELECT email FROM users WHERE email = '{$email}'");

        if (mysqli_num_rows($sql) > 0) {
            echo "$email - already exists";
        } else {
            if (isset($_FILES['image'])) {
                $img_name = $_FILES['image']['name'];
                $img_type = $_FILES['image']['type'];
                $tmp_name = $_FILES['image']['tmp_name'];

                $img_explode = explode('.', $img_name);
                $img_Ext = end($img_explode);

                $extensions = ['jpg', 'jpeg', 'png'];
                if (in_array($img_Ext, $extensions) === true) {
                    $time = time();
                    $new_img_name = $time.$img_name;
                    if (move_uploaded_file($tmp_name, "images/".$new_img_name)) {
                        $status = "Active now";
                        $random_id = rand(time(), 10000000);

                        // ✅ HASH the password before saving
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        $sql2 = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status, designation, location, employee_code)
                                VALUES ({$random_id}, '{$fname}', '{$lname}', '{$email}', '{$hashed_password}', '{$new_img_name}', '{$status}', '{$designation}', '{$location}', '{$employee_code}')");

                        if ($sql2) {
                            $sql3 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                            if (mysqli_num_rows($sql3) > 0) {
                                $row = mysqli_fetch_assoc($sql3);
                                $_SESSION['unique_id'] = $row['unique_id'];
                                echo "success";
                            }
                        }
                    }
                } else {
                    echo "Invalid image type. Only JPG, JPEG, and PNG are allowed.";
                }
            } else {
                echo "Please select an image file.";
            }
        }
    } else {
        echo "$email - This email is not valid";
    }
} else {
    echo "All input fields are required";
}
?>