<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit;
}
include_once "../php/config.php";

$unique_id = $_SESSION['unique_id'];
$sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = '{$unique_id}'");
if (!$sql || mysqli_num_rows($sql) == 0) {
    die("User not found.");
}
$user = mysqli_fetch_assoc($sql);
?>

<?php include_once "../public/header.php"; ?>
<body>
  <div class="wrapper">
    <h2>Settings</h2>
    <form action="../php/update-profile.php" method="POST" enctype="multipart/form-data">
      <div class="profile-pic">
        <img src="../php/images/<?php echo $user['img']; ?>" alt="Profile Picture" width="100" />
        <input type="file" name="image" accept="image/*">
      </div>

      <input type="hidden" name="unique_id" value="<?php echo $user['unique_id']; ?>" />

      <div class="input-group">
        <label>First Name</label>
        <input type="text" name="fname" value="<?php echo $user['fname']; ?>" required>
      </div>

      <div class="input-group">
        <label>Last Name</label>
        <input type="text" name="lname" value="<?php echo $user['lname']; ?>" required>
      </div>

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
      </div>

      <div class="input-group">
        <label>Designation</label>
        <input type="text" name="designation" value="<?php echo $user['designation']; ?>">
      </div>

      <div class="input-group">
        <label>Location</label>
        <input type="text" name="location" value="<?php echo $user['location']; ?>">
      </div>

      <div class="input-group">
        <label>Employee Code</label>
        <input type="text" name="employee_code" value="<?php echo $user['employee_code']; ?>">
      </div>

      <div class="input-group">
        <label>New Password (optional)</label>
        <input type="password" name="password" placeholder="Enter new password if you want to change">
      </div>

      <button type="submit">Update Profile</button>
    </form>

    <a href="help.php" class="btn-help">Need Help?</a>
  </div>
</body>
</html>

