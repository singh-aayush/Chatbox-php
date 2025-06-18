<?php
    session_start();
    if(isset($_SESSION['unique_id'])){
        header("location: users.php");
    }
?>
<?php include_once "../public/header.php"; ?>
<body>

    <div class="wrapper">
        <section class="form signup">
            <header> Realtime chat app</header>
            <form action="#" enctype="multipart/form-data">
                <div class="error-txt"></div>
                <div class="name-details">
                    <div class="field input">
                        <label>First Name</label>
                        <input type="text" name="fname" placeholder="First Name" required>
                    </div>

                    <div class="field input">
                        <label>Last Name</label>
                        <input type="text" name="lname" placeholder="Last Name" required>
                    </div>
                </div>
            <div class="name-details">
                <div class="field input">
                        <label>Employee_code</label>
                        <input type="text" name="employee_code" placeholder="Enter your Emp Id" required>
                        
                    </div>

                    <div class="field input">
                        <label>Designation</label>
                        <input type="text" name="designation" placeholder="Enter Your job role" required>
                    </div>
            </div>

                    <div class="field input">
                        <label>Location</label>
                        <input type="text" name="location" placeholder="Enter location" required>
                        
                    </div>

                    <div class="field input">
                        <label>E-mail</label>
                        <input type="email" name="email" placeholder="Enter New email" required>
                        
                    </div>


                    <div class="field input">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter New Password" required>
                        <i class="fas fa-eye"></i>
                    </div>

                    <div class="field image">
                        <label>Select Image</label>
                        <input type="file" name="image" placeholder="image" required>
                    </div>

                     <div class="field button">
                        <input type="submit" value="Continue to chat" required>
                    </div>
                
            </form>
            <div class="link">Already signed up? <a href="./login.php">Login now</a></div>
        </section>

    </div>

   <script src="./js/pass-show-hide.js"></script> 
     <script src="./js/signup.js"></script>
   
    
</body>
</html>