<?php
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/admin-functions.php';
checkAdminAuth();

$stats = [
    'total_users' => 0,
    'total_groups' => 0,
    'total_messages' => 0,
    'online_users' => 0
];

$result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($result) $stats['total_users'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM groups");
if ($result) $stats['total_groups'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT (SELECT COUNT(*) FROM messages) + (SELECT COUNT(*) FROM group_messages) as total");
if ($result) $stats['total_messages'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'Active now'");
if ($result) $stats['online_users'] = $result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    body {
        background: url('./Images/dashboard.jpg') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
    }

    .dashboard-container {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 0;
        padding: 20px;
        min-height: 100vh;
        width: 100%;
    }

    .stat-card {
        border-radius: 12px;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-6px);
    }

    .quick-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: space-evenly;
        background-color: transparent;
        width: 100%;
    }

    .quick-actions .btn {
        flex: 1 1 18%;
        font-size: 1.1rem;
        padding: 20px; 
        border-radius: 10px;
        text-align: center;
        min-width: 180px;
        display: flex;
        flex-direction: column; 
        align-items: center; 
        justify-content: center;
    }

    .quick-actions .btn i {
        font-size: 1.8rem;
        margin-bottom: 8px; 
    }

    .quick-actions .btn span {
        display: block; 
    }

    .card-header {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .logout-btn {
        border-radius: 10px;
        padding: 8px 18px;
        font-weight: bold;
    }

    .card-title {
        font-weight: 600;
    }

    .row.mb-4 {
        margin-left: 0;
        margin-right: 0;
    }

    .row.mb-4 .col-md-3 {
        padding: 10px;
    }

    @media (max-width: 768px) {
        .quick-actions .btn {
            flex: 1 1 45%;
        }
    }

    @media (max-width: 576px) {
        .quick-actions .btn {
            flex: 1 1 100%;
        }
    }
  </style>
</head>
<body>
<div class="container-fluid dashboard-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>
        <span class="text-black">Admin</span>
        <span class="text-warning px-2 rounded">Dashboard</span>
      </h2>
      <a href="../php/admin-logout.php" class="btn btn-danger logout-btn">Logout</a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Users</h6>
                            <h2><?= $stats['total_users'] ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Groups</h6>
                            <h2><?= $stats['total_groups'] ?></h2>
                        </div>
                        <i class="fas fa-users-cog fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Messages</h6>
                            <h2><?= $stats['total_messages'] ?></h2>
                        </div>
                        <i class="fas fa-comments fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card stat-card bg-info text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Online Users</h6>
                            <h2><?= $stats['online_users'] ?></h2>
                        </div>
                        <i class="fas fa-user-check fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="">
                <div class="card-header text-black bg-transparent border-0">
                    <h3>
                        <span class="text-black">Quick</span>
                        <span class="text-warning px-2 rounded">Actions</span>
                    </h3>
                </div>
                <div class="card-body quick-actions">
                    <a href="admin-users.php" class="btn btn-info">
                        <i class="fas fa-users"></i>
                        <span>Manage Users</span>
                    </a>
                    <a href="admin-groups.php" class="btn btn-info">
                        <i class="fas fa-users-cog"></i>
                        <span>Manage Groups</span>
                    </a>
                    <a href="admin-private-chats.php" class="btn btn-info">
                        <i class="fas fa-comments"></i>
                        <span>View Private Chats</span>
                    </a>
                    <a href="admin-create-user.php" class="btn btn-info">
                        <i class="fas fa-user-plus"></i>
                        <span>Create New User</span>
                    </a>
                    <a href="admin-create-group.php" class="btn btn-info">
                        <i class="fas fa-plus-circle"></i>
                        <span>Create New Group</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>