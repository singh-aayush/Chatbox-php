<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
}
?>
<?php include_once "../public/header.php"; ?>
<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <?php
include_once "../php/config.php";
$group_id = isset($_GET['group_id']) ? mysqli_real_escape_string($conn, $_GET['group_id']) : null;

if ($group_id) {
    $sql = mysqli_query($conn, "SELECT * FROM groups WHERE group_id = '{$group_id}'");
    if ($sql && mysqli_num_rows($sql) > 0) {
        $group = mysqli_fetch_assoc($sql);
    } else {
        $group = ['group_name' => 'Unknown Group'];
    }
} else {
    $group = ['group_name' => 'Invalid Group'];
}
?>

        <a href="./users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="../php/images/1749820324penguin.jpg" alt="Group" /> <!-- Use a placeholder group image -->
        <div class="details">
          <span><?php echo $group['group_name']; ?></span>
          <?php
// Check if current user is the group creator
$creator_check = mysqli_query($conn, "SELECT * FROM groups WHERE group_id = '$group_id' AND created_by = '{$_SESSION['unique_id']}'");
if (mysqli_num_rows($creator_check) > 0) {
    echo '<a href="edit-group.php?group_id=' . $group_id . '" class="edit-group-btn" style="margin-left:10px; font-size: 14px;">Edit Group</a>';
}
?>

          <p>Group Chat</p>
        </div>
      </header>

      <div class="chat-box">
        <!-- Group messages will be loaded here via JS -->
      </div>

      <form action="#" class="typing-area" autocomplete="off" enctype="multipart/form-data">
        <input type="file" name="file" id="fileInput" class="file-input" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" hidden>
        <label for="fileInput" class="insert-button">
          <i class="fas fa-paperclip"></i>
        </label>

        <input type="text" name="group_id" value="<?php echo $group_id; ?>" hidden>
        <input type="text" name="sender_id" value="<?php echo $_SESSION['unique_id']; ?>" hidden>
        <input type="text" name="message" class="input-field" placeholder="Type a message here...">
        <button><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
  </div>
  <script src="./js/group-chat.js"></script>
</body>
</html>
