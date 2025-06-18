<?php
session_start();
if(isset($_SESSION['unique_id'])){
    include_once "./config.php";
    $outgoing_id = mysqli_real_escape_string($conn, $_POST['outgoing_id']);
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $output = "";

   $sql = "SELECT messages.*, users.img FROM messages 
        LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
        WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
        OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id})
        ORDER BY msg_id ASC";

    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
    $time = date("g:i a", strtotime($row['created_at'])); // Format time
    $isOutgoing = $row['outgoing_msg_id'] === $outgoing_id;
    $msg = $row['msg'];
    $file = $row['file'];

    // Check if it's a file message
    $fileContent = '';
    if (!empty($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $fileContent = "<img src='{$file}' alt='Image' style='max-width: 200px; max-height: 200px; border-radius: 8px; margin-top: 5px;'>";
        } else {
            $basename = basename($file);
            $fileContent = "<a href='{$file}' download style='color: #2b6cb0; display: inline-block; margin-top: 5px;'>📄 Download {$basename}</a>";
        }
    }

    // Combine message + file (if both exist)
    $messageBody = "<p>{$msg}</p>" . $fileContent;

    if ($isOutgoing) {
    $output .= '<div class="chat outgoing" data-msg-id="' . $row['msg_id'] . '">
                  <div class="details">' . 
                    $messageBody . 
                    '<span class="time">'. $time .'</span>
                    <button class="delete-btn" data-id="' . $row['msg_id'] . '">🗑️</button>
                  </div>
                </div>';
} else {
    $output .= '<div class="chat incoming">
                  <img src="../php/images/'. $row['img'] .'" alt="" />
                  <div class="details">' . 
                    $messageBody . 
                    '<span class="time">'. $time .'</span>
                  </div>
                </div>';
}

}

        echo  $output;
        
    }        


    
}else{
    header("../login.php");
}
?>
<!-- previous code -->

