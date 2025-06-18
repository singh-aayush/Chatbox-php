<?php
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/admin-functions.php';
checkAdminAuth();

// Fetch all users for member selection
$users = [];
$result = $conn->query("SELECT * FROM users");
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle error message
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Create New Group</h2>
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
                <form action="../php/admin-create-group.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="group_name" class="form-label">Group Name</label>
                        <input type="text" class="form-control" id="group_name" name="group_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="group_image" class="form-label">Group Image</label>
                        <input type="file" class="form-control" id="group_image" name="group_image" accept=".jpg,.jpeg,.png">
                    </div>

                    <div class="mb-3">
                        <label for="member_ids" class="form-label">Select Members</label>
                        <select id="member_ids" name="member_ids[]" class="form-control select2" multiple="multiple">
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['unique_id'] ?>">
                                    <?= htmlspecialchars($user['fname'] . ' ' . $user['lname'] . ' (' . $user['email'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Create Group
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
                placeholder: "Select members",
                allowClear: true
            });
        });
    </script>
</body>
</html>