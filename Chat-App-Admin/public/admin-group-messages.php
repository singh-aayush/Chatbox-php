<?php
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/admin-functions.php';
checkAdminAuth();

if (!isset($_GET['id'])) {
    header("Location: admin-groups.php");
    exit;
}

$group_id = sanitizeInput($conn, $_GET['id']);

// Fetch group details
$group_stmt = $conn->prepare("SELECT * FROM groups WHERE group_id = ?");
$group_stmt->bind_param("i", $group_id);
$group_stmt->execute();
$group = $group_stmt->get_result()->fetch_assoc();

if (!$group) {
    header("Location: admin-groups.php");
    exit;
}

// Fetch messages with user details
$messages_stmt = $conn->prepare("
    SELECT gm.*, u.fname, u.lname, u.img 
    FROM group_messages gm 
    JOIN users u ON gm.sender_id = u.unique_id 
    WHERE gm.group_id = ? 
    ORDER BY gm.timestamp ASC
");
$messages_stmt->bind_param("i", $group_id);
$messages_stmt->execute();
$messages = $messages_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Messages - <?= htmlspecialchars($group['group_name']) ?></title>
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
            <h2>Group Messages - <?= htmlspecialchars($group['group_name']) ?></h2>
            <div>
                <!-- <a href="../php/admin-download-group-chats.php?id=<?= $group['group_id'] ?>&format=csv" class="btn btn-sm btn-success">
                    <i class="fas fa-download"></i> CSV
                </a>
                <a href="../php/admin-download-group-chats.php?id=<?= $group['group_id'] ?>&format=txt" class="btn btn-sm btn-info">
                    <i class="fas fa-download"></i> TXT
                </a>
                <a href="../php/admin-download-group-chats.php?id=<?= $group['group_id'] ?>&format=pdf" class="btn btn-sm btn-danger">
                    <i class="fas fa-download"></i> PDF
                </a> -->
                <a href="admin-groups.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Groups
                </a>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Messages</h5>
            </div>
            <div class="card-body">
                <div class="chat-container">
                    <?php foreach ($messages as $message): ?>
                        <div class="message mb-3 <?= $message['sender_id'] == $_SESSION['admin_unique_id'] ? 'text-end' : 'text-start' ?>">
                            <div class="d-flex <?= $message['sender_id'] == $_SESSION['admin_unique_id'] ? 'justify-content-end' : '' ?>">
                                <?php if ($message['sender_id'] != $_SESSION['admin_unique_id']): ?>
                                    <img src="../php/images/<?= htmlspecialchars($message['img'] ?: 'user.jpg') ?>" 
                                         class="rounded-circle me-2" width="40" height="40">
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold"><?= htmlspecialchars($message['fname'] . ' ' . $message['lname']) ?></div>
                                    <div class="message-bubble p-3 rounded <?= $message['sender_id'] == $_SESSION['admin_unique_id'] ? 'bg-primary text-white' : 'bg-light' ?>">
                                        <?= htmlspecialchars($message['message']) ?>
                                        <?php if (!empty($message['file_path'])): ?>
                                            <br><a href="<?= $message['file_path'] ?>" target="_blank">View Attachment</a>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted"><?= date('h:i A, M j', strtotime($message['timestamp'])) ?></small>
                                    <button class="btn btn-sm btn-danger delete-message-btn" data-message-id="<?= $message['message_id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <?php if ($message['sender_id'] == $_SESSION['admin_unique_id']): ?>
                                    <img src="../php/images/<?= htmlspecialchars($message['img'] ?: 'user.jpg') ?>" 
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
    <script>
        $(document).ready(function() {
            $('.delete-message-btn').click(function() {
                var messageId = $(this).data('message-id');
                var messageDiv = $(this).closest('.message');

                if (confirm('Are you sure you want to delete this message?')) {
                    $.post('../php/admin-delete-message.php', { id: messageId })
                        .done(function(response) {
                            if (response === 'success') {
                                messageDiv.remove();
                            } else {
                                alert('Failed to delete message.');
                            }
                        });
                }
            });
        });
    </script>
</body>
</html>