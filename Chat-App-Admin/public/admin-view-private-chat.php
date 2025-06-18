<?php
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/admin-functions.php';
checkAdminAuth();

if (!isset($_GET['user_id1']) || !isset($_GET['user_id2'])) {
    header("Location: admin-private-chats.php");
    exit;
}

$user_id1 = sanitizeInput($conn, $_GET['user_id1']);
$user_id2 = sanitizeInput($conn, $_GET['user_id2']);

// Fetch user details
$user1 = $user2 = null;
$stmt = $conn->prepare("SELECT * FROM users WHERE unique_id = ?");
$stmt->bind_param("i", $user_id1);
$stmt->execute();
$user1 = $stmt->get_result()->fetch_assoc();

$stmt->bind_param("i", $user_id2);
$stmt->execute();
$user2 = $stmt->get_result()->fetch_assoc();

if (!$user1 || !$user2) {
    header("Location: admin-private-chats.php");
    exit;
}

// Fetch messages between these users
$messages = [];
$stmt = $conn->prepare("
    SELECT m.*, s.fname as sender_fname, s.lname as sender_lname, s.img as sender_img 
    FROM messages m 
    JOIN users s ON m.outgoing_msg_id = s.unique_id 
    WHERE (m.outgoing_msg_id = ? AND m.incoming_msg_id = ?) 
    OR (m.outgoing_msg_id = ? AND m.incoming_msg_id = ?) 
    ORDER BY m.created_at ASC
");
$stmt->bind_param("iiii", $user_id1, $user_id2, $user_id2, $user_id1);
$stmt->execute();
$messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Private Chat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .message-bubble {
            max-width: 70%;
            display: inline-block;
            word-wrap: break-word;
        }
        .chat-container {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Private Chat</h2>
            <div>
                <a href="../php/admin-download-private-chat.php?user_id1=<?= $user1['unique_id'] ?>&user_id2=<?= $user2['unique_id'] ?>&format=csv" class="btn btn-sm btn-success">
                    <i class="fas fa-download"></i> CSV
                </a>
                <a href="../php/admin-download-private-chat.php?user_id1=<?= $user1['unique_id'] ?>&user_id2=<?= $user2['unique_id'] ?>&format=txt" class="btn btn-sm btn-info">
                    <i class="fas fa-download"></i> TXT
                </a>
                <a href="../php/admin-download-private-chat.php?user_id1=<?= $user1['unique_id'] ?>&user_id2=<?= $user2['unique_id'] ?>&format=pdf" class="btn btn-sm btn-danger">
                    <i class="fas fa-download"></i> PDF
                </a>
                <a href="admin-private-chats.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Chats
                </a>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    Conversation between 
                    <img src="../php/images/<?= htmlspecialchars($user1['img'] ?: 'user.jpg') ?>" 
                         class="rounded-circle" width="30" height="30">
                    <?= htmlspecialchars($user1['fname'] . ' ' . $user1['lname']) ?> and
                    <img src="../php/images/<?= htmlspecialchars($user2['img'] ?: 'user.jpg') ?>" 
                         class="rounded-circle" width="30" height="30">
                    <?= htmlspecialchars($user2['fname'] . ' ' . $user2['lname']) ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="chat-container">
                    <?php foreach ($messages as $message): ?>
                        <div class="message mb-3 <?= $message['outgoing_msg_id'] == $user1['unique_id'] ? 'text-start' : 'text-end' ?>">
                            <div class="d-flex <?= $message['outgoing_msg_id'] == $user1['unique_id'] ? '' : 'justify-content-end' ?>">
                                <?php if ($message['outgoing_msg_id'] == $user1['unique_id']): ?>
                                    <img src="../php/images/<?= htmlspecialchars($user1['img'] ?: 'user.jpg') ?>" 
                                         class="rounded-circle me-2" width="40" height="40">
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold"><?= htmlspecialchars($message['sender_fname'] . ' ' . $message['sender_lname']) ?></div>
                                    <div class="message-bubble p-3 rounded <?= $message['outgoing_msg_id'] == $user1['unique_id'] ? 'bg-light' : 'bg-primary text-white' ?>">
                                        <?= htmlspecialchars($message['msg']) ?>
                                    </div>
                                    <small class="text-muted"><?= date('h:i A, M j', strtotime($message['created_at'])) ?></small>
                                </div>
                                <?php if ($message['outgoing_msg_id'] == $user2['unique_id']): ?>
                                    <img src="../php/images/<?= htmlspecialchars($user2['img'] ?: 'user.jpg') ?>" 
                                         class="rounded-circle ms-2" width="40" height="40">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>