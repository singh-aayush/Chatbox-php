<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "./config.php";

    if (!isset($_POST['group_id']) || !is_numeric($_POST['group_id'])) {
        echo "Error: Invalid group_id.";
        exit;
    }

    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
    $sender_id = $_SESSION['unique_id'];
    $output = "";

    $sql = "SELECT gm.*, users.img, users.fname, users.lname 
            FROM group_messages gm
            LEFT JOIN users ON users.unique_id = gm.sender_id
            WHERE gm.group_id = {$group_id}
            ORDER BY gm.timestamp ASC";

    $query = mysqli_query($conn, $sql);

    if (!$query) {
        echo "SQL Error: " . mysqli_error($conn);
        exit;
    }

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            // Use timestamp instead of created_at
            $time = date("g:i a", strtotime($row['timestamp']));
            
            // Use message instead of msg
            $message_content = !empty($row['message']) ? $row['message'] : '';
            
            // Check if file exists
            $file_content = '';
            if (!empty($row['file_path'])) {
                $file_name = basename($row['file_path']);
                $file_content = '<div class="file-attachment"><a href="'.$row['file_path'].'" download>📎 '.$file_name.'</a></div>';
            }

            if ($row['sender_id'] === $sender_id) {
                $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>'.$message_content.'</p>
                                    '.$file_content.'
                                    <span class="time">'.$time.'</span>
                                    <button class="delete-btn" data-id="' . $row['message_id'] . '">🗑️</button>
                                </div>
                            </div>';
            } else {
                $output .= '<div class="chat incoming">
                                <img src="../php/images/'.$row['img'].'" alt="">
                                <div class="details">
                                    <p><strong>'.$row['fname'].':</strong> '.$message_content.'</p>
                                    '.$file_content.'
                                    <span class="time">'.$time.'</span>
                                </div>
                            </div>';
            }
        }
        echo $output;
    } else {
        echo "<p class='text'>No messages yet.</p>";
    }
} else {
    header("Location: ../login.php");
}
?>