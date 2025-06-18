<?php
    session_start();
    if(!isset($_SESSION['unique_id'])){
        header("location: login.php");
    }
?>
<?php include_once "header.php"; ?>
<body>

    <div class="wrapper">
        <section class="users">
           <header>
            <?php
            include_once "../php/config.php";
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = '{$_SESSION['unique_id']}'");
            if (mysqli_num_rows($sql) > 0){ 
              $row = mysqli_fetch_assoc($sql);
            }
            ?>
             <div class="content">
                <img src="../php/images/<?php echo $row['img'] ?>" alt="">
                <div class="details">
                    <span><?php echo $row['fname'] . " " . $row['lname'] ?></span>
                    <p><?php echo $row['status'] ?></p>
                </div>
            </div>
            <a href="../php/logout.php?logout_id=<?php echo $row['unique_id']?>" class="logout">Logout</a>
            <a href="settings.php" class="settings-btn">⚙️ Settings</a>
           </header>

           <a href="create-group.php" class="create-group-btn">+ Create Group</a>
           <a href="chat-requests.php" class="chat-request-link">Chat Requests</a>

<!-- Group List Section -->
<div class="group-list">
    <h3>Groups</h3>
    <?php
    include_once "../php/config.php";
    $unique_id = $_SESSION['unique_id'];

    $group_query = mysqli_query($conn, 
        "SELECT g.group_id, g.group_name 
         FROM groups g 
         JOIN group_members gm ON g.group_id = gm.group_id 
         WHERE gm.unique_id = '{$unique_id}'");

    if (mysqli_num_rows($group_query) > 0) {
        while ($group = mysqli_fetch_assoc($group_query)) {
            echo '<div class="group-item">';
            echo '<a href="group-chat.php?group_id=' . $group['group_id'] . '">' . htmlspecialchars($group['group_name']) . '</a>';
            echo '</div>';
        }
    } else {
        echo "<p>No groups yet.</p>";
    }
    ?>
</div>





           <div class="search">
            <span class="text">
                Select a user to start chat
            </span>
            <input type="text" placeholder="Enter name to search...">
            <button><i class="fas fa-search"></i></button>
           </div>
           <div class="users-list">
            
             
           </div>
           
        </section>

    </div>
<script src="js/users.js"></script>
    
    
</body>
</html>