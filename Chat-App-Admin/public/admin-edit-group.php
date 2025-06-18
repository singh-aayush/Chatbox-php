<?php
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/admin-functions.php';
checkAdminAuth();

if (!isset($_GET['id'])) {
    header("Location: admin-groups.php");
    exit;
}

$group_id = sanitizeInput($conn, $_GET['id']);

// Fetch group details and members
$group = ['members' => []];
$stmt = $conn->prepare("
    SELECT g.*, u.fname, u.lname, u.email, gm.unique_id, gm.is_admin 
    FROM groups g 
    LEFT JOIN group_members gm ON g.group_id = gm.group_id 
    LEFT JOIN users u ON gm.unique_id = u.unique_id 
    WHERE g.group_id = ?
");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    if (empty($group['group_id'])) {
        $group = [
            'group_id' => $row['group_id'],
            'group_name' => $row['group_name'],
            'group_image' => $row['group_image']
        ];
    }
    if ($row['unique_id']) {
        $group['members'][] = [
            'unique_id' => $row['unique_id'],
            'fname' => $row['fname'],
            'lname' => $row['lname'],
            'email' => $row['email'],
            'is_admin' => $row['is_admin']
        ];
    }
}

// Fetch all users for adding new members
$all_users = [];
$result = $conn->query("SELECT * FROM users");
if ($result) {
    $all_users = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle error message
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Group</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
        }
        .member-list {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Group: <?= htmlspecialchars($group['group_name']) ?></h2>
            <a href="admin-groups.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Back to Groups
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Group Details</h5>
            </div>
            <div class="card-body">
                <form action="../php/admin-edit-group.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $group['group_id'] ?>">
                    
                    <div class="mb-3">
                        <label for="group_name" class="form-label">Group Name</label>
                        <input type="text" class="form-control" id="group_name" name="group_name" 
                               value="<?= htmlspecialchars($group['group_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="group_image" class="form-label">Group Image</label>
                        <input type="file" class="form-control" id="group_image" name="group_image" accept=".jpg,.jpeg,.png">
                        <?php if (!empty($group['group_image'])): ?>
                            <div class="mt-2">
                                <img src="../php/images/<?= htmlspecialchars($group['group_image']) ?>" 
                                     class="img-thumbnail" width="100" height="100">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_image" id="removeImage">
                                    <label class="form-check-label" for="removeImage">Remove Current Image</label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Members</label>
                        <div class="member-list border p-3 mb-3">
                            <?php if (empty($group['members'])): ?>
                                <div class="text-muted">No members in this group</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($group['members'] as $member): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($member['fname'] . ' ' . $member['lname']) ?></td>
                                                    <td><?= htmlspecialchars($member['email']) ?></td>
                                                    <td>
                                                        <span class="badge <?= $member['is_admin'] ? 'bg-success' : 'bg-secondary' ?>">
                                                            <?= $member['is_admin'] ? 'Admin' : 'Member' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-warning toggle-admin-btn" 
                                                                data-group-id="<?= $group['group_id'] ?>" 
                                                                data-user-id="<?= $member['unique_id'] ?>">
                                                            <i class="fas fa-user-shield"></i> Toggle Admin
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger remove-member-btn" 
                                                                data-group-id="<?= $group['group_id'] ?>" 
                                                                data-user-id="<?= $member['unique_id'] ?>">
                                                            <i class="fas fa-user-minus"></i> Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="new_member_ids" class="form-label">Add New Members</label>
                        <select id="new_member_ids" name="new_member_ids[]" class="form-control select2" multiple="multiple">
                            <?php foreach ($all_users as $user): 
                                $is_member = false;
                                foreach ($group['members'] as $member) {
                                    if ($member['unique_id'] == $user['unique_id']) {
                                        $is_member = true;
                                        break;
                                    }
                                }
                                if (!$is_member): ?>
                                    <option value="<?= $user['unique_id'] ?>">
                                        <?= htmlspecialchars($user['fname'] . ' ' . $user['lname'] . ' (' . $user['email'] . ')') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Update Group
                        </button>
                        <a href="admin-groups.php" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select members to add",
                allowClear: true
            });

            $('.toggle-admin-btn').click(function() {
                const groupId = $(this).data('group-id');
                const userId = $(this).data('user-id');
                
                $.post('../php/admin-toggle-group-admin.php', {
                    group_id: groupId,
                    user_id: userId
                }).done(function(response) {
                    if (response === 'success') {
                        location.reload();
                    } else {
                        alert('Failed to update admin status');
                    }
                });
            });

            $('.remove-member-btn').click(function() {
                if (confirm('Are you sure you want to remove this member?')) {
                    const groupId = $(this).data('group-id');
                    const userId = $(this).data('user-id');
                    
                    $.post('../php/admin-remove-group-member.php', {
                        group_id: groupId,
                        user_id: userId
                    }).done(function(response) {
                        if (response === 'success') {
                            location.reload();
                        } else {
                            alert('Failed to remove member');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>