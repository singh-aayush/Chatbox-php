<?php
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/admin-functions.php';
checkAdminAuth();

// Fetch all unique conversations with user details
$conversations = [];
$result = $conn->query("
    SELECT 
        m.*, 
        s.fname as sender_fname, s.lname as sender_lname, s.img as sender_img, 
        r.fname as receiver_fname, r.lname as receiver_lname, r.img as receiver_img 
    FROM messages m 
    JOIN users s ON m.outgoing_msg_id = s.unique_id 
    JOIN users r ON m.incoming_msg_id = r.unique_id 
    ORDER BY m.created_at DESC
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $key = min($row['outgoing_msg_id'], $row['incoming_msg_id']) . '-' . 
               max($row['outgoing_msg_id'], $row['incoming_msg_id']);
        if (!isset($conversations[$key]) || strtotime($row['created_at']) > strtotime($conversations[$key]['created_at'])) {
            $conversations[$key] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Private Chats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Private Chats</h2>
            <a href="admin-dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
        </div>

        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">All Conversations</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="conversationsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Participants</th>
                                <th>Last Message</th>
                                <th>Timestamp</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($conversations as $conversation): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../php/images/<?= htmlspecialchars($conversation['sender_img'] ?: 'user.jpg') ?>" 
                                                 class="rounded-circle me-2" width="40" height="40">
                                            <span><?= htmlspecialchars($conversation['sender_fname'] . ' ' . $conversation['sender_lname']) ?></span>
                                            <span class="mx-2">and</span>
                                            <img src="../php/images/<?= htmlspecialchars($conversation['receiver_img'] ?: 'user.jpg') ?>" 
                                                 class="rounded-circle me-2" width="40" height="40">
                                            <span><?= htmlspecialchars($conversation['receiver_fname'] . ' ' . $conversation['receiver_lname']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars(substr($conversation['msg'], 0, 50)) . (strlen($conversation['msg']) > 50 ? '...' : '') ?></td>
                                    <td><?= date('M j, h:i A', strtotime($conversation['created_at'])) ?></td>
                                    <td>
                                        <a href="../php/admin-view-private-chat.php?user_id1=<?= $conversation['outgoing_msg_id'] ?>&user_id2=<?= $conversation['incoming_msg_id'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <div class="btn-group">
                                            <!-- <a href="../php/admin-download-private-chat.php?user_id1=<?= $conversation['outgoing_msg_id'] ?>&user_id2=<?= $conversation['incoming_msg_id'] ?>&format=txt" 
                                               class="btn btn-sm btn-info" title="Download as TXT">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                            <a href="../php/admin-download-private-chat.php?user_id1=<?= $conversation['outgoing_msg_id'] ?>&user_id2=<?= $conversation['incoming_msg_id'] ?>&format=pdf" 
                                               class="btn btn-sm btn-danger" title="Download as PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div> -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#conversationsTable').DataTable({
                order: [[2, 'desc']],
                responsive: true
            });
        });
    </script>
</body>
</html>