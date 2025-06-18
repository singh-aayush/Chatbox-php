<?php
    session_start();
    if(isset($_SESSION['unique_id'])){
        header("location: users.php");
    }
?>
<?php include_once "../public/header.php"; ?>
<body>

    <div class="wrapper">
        <section class="form login">
            <header> Realtime chat app</header>
            <form action="#">
                <div class="error-txt"></div>
                
                    <div class="field input">
                        <label>Email</label>
                        <input type="text" name="email" placeholder="Enter Your Email" required>
                    </div>

                    <div class="field input">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter Your Password" required>
                        <i class="fas fa-eye"></i>
                    </div>

                   

                     <div class="field button">
                        <input type="submit" value="Continue to chat">
                    </div>
                
            </form>
            <div class="link">Not yet signed up? <a href="./index.php">Signup now</a></div>
        </section>

    </div>

     <script src="./js/pass-show-hide.js"></script>
     <script src="./js/login.js"></script>
  
    
</body>
</html>