<?php
 while($row = mysqli_fetch_assoc($sql)){
    $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['unique_id']}
             OR outgoing_msg_id = {$row['unique_id']}) AND (incoming_msg_id = {$outgoing_id}
             OR outgoing_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";

    $query2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($query2);

    if($row2 !== null){
        $result = $row2['msg'];
        $you = ($outgoing_id == $row2['outgoing_msg_id']) ? "You: " : "";
    } else {
        $result = "No msgs available";
        $you = "";
    }

    // Trim the message
    $msg = (strlen($result) > 28) ? substr($result, 0, 28) . '...' : $result;

    //check user is online or offline
    ($row['status'] == "Offline now") ? $offline = "offline" : $offline = "";

    $output .= '<a href="chat.php?user_id=' . $row['unique_id'] . '">
                <div class="content">
                    <img src="../php/images/' . $row['img'] . '" alt="">
                    <div class="details">
                        <span>' . $row['fname'] . " " . $row['lname'] . '</span>
                        <p>' . $you . $msg . '</p>
                    </div>
                </div>
                <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                </a>';
}


?>