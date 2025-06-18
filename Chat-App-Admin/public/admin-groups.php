<?php
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/admin-functions.php';
checkAdminAuth();

// Fetch all groups with member counts
$groups = [];
$result = $conn->query("
    SELECT g.*, COUNT(gm.unique_id) as member_count 
    FROM groups g 
    LEFT JOIN group_members gm ON g.group_id = gm.group_id 
    GROUP BY g.group_id
");
if ($result) {
    $groups = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle messages
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Groups</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Groups</h2>
            <div>
                <a href="admin-dashboard.php" class="btn btn-secondary mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Dashboard
                </a>
                <a href="admin-create-group.php" class="btn btn-success">
                    <i class="fas fa-plus-circle mr-1"></i> Create Group
                </a>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">All Groups</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="groupsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Group Name</th>
                                <th>Image</th>
                                <th>Members</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groups as $group): ?>
                                <tr>
                                    <td><?= htmlspecialchars($group['group_name']) ?></td>
                                    <td>
                                        <img src="../php/images/<?= htmlspecialchars($group['group_image'] ?: 'team.png') ?>" 
                                             class="rounded-circle" width="40" height="40" alt="Group Image">
                                    </td>
                                    <td><?= $group['member_count'] ?></td>
                                    <td>
                                        <a href="../php/admin-group-messages.php?id=<?= $group['group_id'] ?>" 
                                           class="btn btn-sm btn-info" title="View Messages">
                                            <i class="fas fa-comments"></i>
                                        </a>
                                        <a href="admin-edit-group.php?id=<?= $group['group_id'] ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="../php/admin-delete-group.php" method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $group['group_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this group?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
            $('#groupsTable').DataTable({
                responsive: true
            });
        });
    </script>
</body>
</html>