<?php
// admin-download-group-chats.php
include_once "C:/xampp/htdocs/Chat-App/php/config.php";
include_once __DIR__ . "/admin-functions.php";
require_once 'vendor/autoload.php';
checkAdminAuth();

if (isset($_GET['id']) && isset($_GET['format'])) {
    $group_id = sanitizeInput($conn, $_GET['id']);
    $format = $_GET['format'];
    
    // Get group info
    $stmt = $conn->prepare("SELECT group_name FROM groups WHERE group_id = ?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $group = $stmt->get_result()->fetch_assoc();
    
    if (!$group) {
        header("Location: ../public/admin-groups.php?error=Group+not+found");
        exit;
    }

    // Get messages
    $stmt = $conn->prepare("SELECT gm.*, u.fname, u.lname 
                          FROM group_messages gm 
                          JOIN users u ON gm.sender_id = u.unique_id 
                          WHERE gm.group_id = ? 
                          ORDER BY gm.timestamp ASC");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Generate output based on format
    switch (strtolower($format)) {
        case 'txt':
            $output = "Chat messages for group: {$group['group_name']}\n";
            $output .= "====================================\n\n";
            foreach ($messages as $message) {
                $output .= "[{$message['timestamp']}] {$message['fname']} {$message['lname']}:\n";
                $output .= $message['message'] . "\n\n";
            }
            header('Content-Type: text/plain');
            header("Content-Disposition: attachment; filename=\"{$group['group_name']}_messages.txt\"");
            echo $output;
            break;

        case 'pdf':
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle("Chat History: {$group['group_name']}");
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 18);
            $pdf->Cell(0, 10, "Chat History: {$group['group_name']}", 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', '', 12);

            foreach ($messages as $message) {
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(0, 10, "[{$message['timestamp']}] {$message['fname']} {$message['lname']}:", 0, 1);
                $pdf->SetFont('helvetica', '', 12);
                $pdf->MultiCell(0, 10, $message['message'], 0, 'L');
                $pdf->Ln(5);
            }

            $pdf->Output("{$group['group_name']}_messages.pdf", 'D');
            break;

        default: // CSV
            $output = "Sender,Message,Timestamp\n";
            foreach ($messages as $message) {
                $output .= "\"{$message['fname']} {$message['lname']}\",\"{$message['message']}\",\"{$message['timestamp']}\"\n";
            }
            header('Content-Type: text/csv');
            header("Content-Disposition: attachment; filename=\"{$group['group_name']}_messages.csv\"");
            echo $output;
            break;
    }
    exit;
}

header("Location: ../public/admin-groups.php");
?>