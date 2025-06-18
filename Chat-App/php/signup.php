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

if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
     // check email is valid or not
     if(filter_var($email, FILTER_VALIDATE_EMAIL)){ //if email is valid

        // email already exists in database
         $sql = mysqli_query($conn, "SELECT email FROM users WHERE email = '{$email}'");

          if (mysqli_num_rows($sql) > 0) {
          echo "$email - already exists";
          }else{
            //user upload a file or not
            if (isset($_FILES['image'])){
            // File is uploaded
            $img_name = $_FILES['image']['name'];
            $img_type = $_FILES['image']['type'];
            $tmp_name = $_FILES['image']['tmp_name'];


            //explode the img and get the extention

            $img_explode = explode('.', $img_name);
            $img_Ext = end($img_explode); //here we get the extention of image
            
            $extentions = ['jpg', 'jpeg', 'png'];
             if (in_array($img_Ext, $extentions) === true){
                $time = time();
                // move the user image to a particular folder 
                 $new_img_name = $time.$img_name;
                if( move_uploaded_file($tmp_name, "images/".$new_img_name)){
                           $status ="Active now";
                           $random_id = rand(time(), 10000000);

                           // insert user data into table
                          $sql2 = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status, designation, location, employee_code)
                                              VALUES ({$random_id}, '{$fname}', '{$lname}', '{$email}', '{$password}', '{$new_img_name}', '{$status}', '{$designation}', '{$location}', '{$employee_code}')");

                           if($sql2){
                            $sql3 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                            if(mysqli_num_rows($sql3) > 0){
                                $row = mysqli_fetch_assoc($sql3);
                                $_SESSION['unique_id'] = $row['unique_id'];
                                echo "success";
                            }
                           }                  
                }
         

             }else{
                 echo "Invalid image type. Only JPG, JPEG, and PNG are allowed.";
             }

            
            
            
            
        } else {
            echo "Please select a image file";
        }
           
          }


     }else{ echo "$email - This email is not valid";

     }
}else{
    echo "All input fields are required";
}

?>