<?php
session_start();
require_once '../php/config.php';

$group_id = $_GET['group_id'];
$user_id = $_SESSION['unique_id'];

// Fetch group only if current user is the creator
$query = "SELECT * FROM groups WHERE group_id = $group_id AND created_by = $user_id";
$result = mysqli_query($conn, $query);
$group = mysqli_fetch_assoc($result);

// Fetch all users
$users = mysqli_query($conn, "SELECT * FROM users WHERE unique_id != $user_id");

// Fetch current group members
$members = mysqli_query($conn, "SELECT unique_id FROM group_members WHERE group_id = $group_id");

$current_members = [];
while ($row = mysqli_fetch_assoc($members)) {
    $current_members[] = $row['unique_id'];
}
?>

<h2>Edit Group: <?= htmlspecialchars($group['group_name']) ?></h2>

<form action="update-group.php" method="POST">
    <input type="hidden" name="group_id" value="<?= $group_id ?>">

    <label>Group Name:</label>
    <input type="text" name="group_name" value="<?= htmlspecialchars($group['group_name']) ?>" required><br>

    <label>Add/Remove Members:</label><br>
    <?php while ($user = mysqli_fetch_assoc($users)) { ?>
        <input type="checkbox" name="members[]" value="<?= $user['unique_id'] ?>"
            <?= in_array($user['unique_id'], $current_members) ? 'checked' : '' ?>>
        <?= $user['fname'] . ' ' . $user['lname'] ?><br>
    <?php } ?>

    <button type="submit" name="update_group">Update Group</button>
</form>

