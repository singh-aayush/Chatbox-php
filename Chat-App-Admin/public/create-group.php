<?php 
  session_start();
  if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
  }
  include_once "../php/config.php";
?>

<!DOCTYPE html>
<html>
<head>
  <title>Create Group</title>
</head>
<body>
  <h2>Create a New Group</h2>
  <form action="../php/create-group.php" method="POST" enctype="multipart/form-data">
    <label>Group Name:</label>
    <input type="text" name="group_name" required><br><br>

    <label>Group Image:</label>
  <input type="file" name="group_image" accept="image/*"><br><br>

    <label>Select Members:</label><br>
    <?php
      $user_id = $_SESSION['unique_id'];
      $query = mysqli_query($conn, "SELECT * FROM users WHERE unique_id != $user_id");
      while($row = mysqli_fetch_assoc($query)){
        echo '<input type="checkbox" name="members[]" value="'.$row['unique_id'].'"> '.$row['fname'].' '.$row['lname'].'<br>';
      }
    ?><br>

    <input type="submit" value="Create Group">
  </form>
</body>
</html>
