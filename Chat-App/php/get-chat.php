<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION['unique_id'])) {
    include_once "./config.php";

    // Check if required POST data is set
    if (!isset($_POST['outgoing_id']) || !isset($_POST['incoming_id'])) {
        echo "Error: Missing outgoing_id or incoming_id";
        exit;
    }

    $outgoing_id = mysqli_real_escape_string($conn, $_POST['outgoing_id']);
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $output = "";

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT messages.*, users.img FROM messages 
            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
            WHERE (outgoing_msg_id = ? AND incoming_msg_id = ?)
            OR (outgoing_msg_id = ? AND incoming_msg_id = ?)
            ORDER BY msg_id ASC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iiii", $outgoing_id, $incoming_id, $incoming_id, $outgoing_id);
        mysqli_stmt_execute($stmt);
        $query = mysqli_stmt_get_result($stmt);

        if ($query && mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $time = date("g:i a", strtotime($row['created_at'])); // Format time
                $isOutgoing = $row['outgoing_msg_id'] === $outgoing_id;
                $msg = $row['msg'];
                $file = $row['file'];

                // Check if it's a file message
                $fileContent = '';
                if (!empty($file)) {
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
                        $fileContent = "<img src='{$file}' alt='Image' style='max-width: 200px; max-height: 200px; border-radius: 8px; margin-top: 5px;'>";
                    } else {
                        $basename = basename($file);
                        $fileContent = "<a href='{$file}' download style='color: #2b6cb0; display: inline-block; margin-top: 5px;'>📄 Download {$basename}</a>";
                    }
                }

                // Combine message + file (if both exist)
                $messageBody = $msg ? "<p>" . htmlspecialchars($msg) . "</p>" : "";
                $messageBody .= $fileContent;

                if ($isOutgoing) {
                    $output .= '<div class="chat outgoing" data-msg-id="' . htmlspecialchars($row['msg_id']) . '">
                                <div class="details">' . 
                                $messageBody . 
                                '<span class="time">' . $time . '</span>
                                <button class="delete-btn" data-id="' . htmlspecialchars($row['msg_id']) . '">🗑️</button>
                                </div>
                              </div>';
                } else {
                    $output .= '<div class="chat incoming">
                                <img src="../php/images/' . htmlspecialchars($row['img']) . '" alt="" />
                                <div class="details">' . 
                                $messageBody . 
                                '<span class="time">' . $time . '</span>
                                </div>
                              </div>';
                }
            }
            echo $output;
        } else {
            echo '<div class="chat-placeholder">No messages yet</div>';
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Failed to prepare SQL statement";
    }
} else {
    // Proper redirect with Location header
    header("Location: ../login.php");
    exit;
}

mysqli_close($conn);
?>