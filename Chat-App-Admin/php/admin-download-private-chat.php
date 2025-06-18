<?php
// admin-download-private-chat.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
require_once 'vendor/autoload.php';
checkAdminAuth();

if (isset($_GET['user_id1']) && isset($_GET['user_id2']) && isset($_GET['format'])) {
    $user_id1 = sanitizeInput($conn, $_GET['user_id1']);
    $user_id2 = sanitizeInput($conn, $_GET['user_id2']);
    $format = $_GET['format'];
    
    // Get user info
    $stmt = $conn->prepare("SELECT fname FROM users WHERE unique_id = ?");
    $stmt->bind_param("i", $user_id1);
    $stmt->execute();
    $user1 = $stmt->get_result()->fetch_assoc();
    
    $stmt->bind_param("i", $user_id2);
    $stmt->execute();
    $user2 = $stmt->get_result()->fetch_assoc();
    
    if (!$user1 || !$user2) {
        header("Location: ../public/admin-private-chats.php?error=Users+not+found");
        exit;
    }

    // Get messages
    $stmt = $conn->prepare("SELECT m.*, s.fname as sender_fname, s.lname as sender_lname 
                          FROM messages m 
                          JOIN users s ON m.outgoing_msg_id = s.unique_id 
                          WHERE (m.outgoing_msg_id = ? AND m.incoming_msg_id = ?) 
                          OR (m.outgoing_msg_id = ? AND m.incoming_msg_id = ?) 
                          ORDER BY m.created_at ASC");
    $stmt->bind_param("iiii", $user_id1, $user_id2, $user_id2, $user_id1);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $file_name = "{$user1['fname']}_and_{$user2['fname']}_chat.{$format}";

    switch (strtolower($format)) {
        case 'txt':
            $output = "Chat between {$user1['fname']} and {$user2['fname']}\n";
            $output .= "=========================================\n\n";
            foreach ($messages as $message) {
                $sender_name = $message['sender_fname'] . " " . $message['sender_lname'];
                $output .= "[{$message['created_at']}] {$sender_name}:\n";
                $output .= $message['msg'] . "\n\n";
            }
            header('Content-Type: text/plain');
            header("Content-Disposition: attachment; filename=\"$file_name\"");
            echo $output;
            break;

        case 'pdf':
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle("Chat History: {$user1['fname']} and {$user2['fname']}");
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 18);
            $pdf->Cell(0, 10, "Chat History: {$user1['fname']} and {$user2['fname']}", 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', '', 12);

            foreach ($messages as $message) {
                $sender_name = $message['sender_fname'] . " " . $message['sender_lname'];
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(0, 10, "[{$message['created_at']}] {$sender_name}:", 0, 1);
                $pdf->SetFont('helvetica', '', 12);
                $pdf->MultiCell(0, 10, $message['msg'], 0, 'L');
                $pdf->Ln(5);
            }

            $pdf->Output($file_name, 'D');
            break;

        default: // CSV
            $output = "Sender,Receiver,Message,Timestamp\n";
            foreach ($messages as $message) {
                $sender_name = $message['sender_fname'] . " " . $message['sender_lname'];
                $receiver_name = ($message['outgoing_msg_id'] == $user_id1) ? $user2['fname'] : $user1['fname'];
                $output .= "\"{$sender_name}\",\"{$receiver_name}\",\"{$message['msg']}\",\"{$message['created_at']}\"\n";
            }
            header('Content-Type: text/csv');
            header("Content-Disposition: attachment; filename=\"$file_name\"");
            echo $output;
            break;
    }
    exit;
}

header("Location: ../public/admin-private-chats.php");
?>