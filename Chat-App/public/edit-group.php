<?php
session_start();
require_once '../php/config.php';

$group_id = $_GET['group_id'];
$user_id = $_SESSION['unique_id'];

// Fetch group only if current user is the creator
$query = "SELECT * FROM groups WHERE group_id = $group_id AND created_by = $user_id";
$result = mysqli_query($conn, $query);
$group = mysqli_fetch_assoc($result);

// Fetch all users
$users = mysqli_query($conn, "SELECT * FROM users WHERE unique_id != $user_id");

// Fetch current group members
$members = mysqli_query($conn, "SELECT unique_id FROM group_members WHERE group_id = $group_id");

$current_members = [];
while ($row = mysqli_fetch_assoc($members)) {
    $current_members[] = $row['unique_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Group</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            overflow: auto;
        }

        .wrapper {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out;
        }

        .form {
            padding: 20px;
            text-align: center;
        }

        .header-animation {
            font-size: 1.8rem;
            color: #00695c;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            animation: bounceIn 0.8s ease-out;
        }

        .field {
            margin-bottom: 15px;
            text-align: left;
        }

        .field label {
            display: block;
            color: #26a69a;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .field input[type="text"],
        .field input[type="hidden"] {
            width: 100%;
            padding: 8px;
            border: 2px solid #e0f2f1;
            border-radius: 20px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .field input[type="text"]:focus {
            border-color: #00695c;
            box-shadow: 0 0 5px rgba(0, 105, 92, 0.2);
        }

        .members-list {
            max-height: 200px;
            overflow-y: auto;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #e0f2f1;
            border-radius: 10px;
        }

        .members-list label {
            display: flex;
            align-items: center;
            margin: 5px 0;
            color: #444;
        }

        .members-list input[type="checkbox"] {
            margin-right: 10px;
            cursor: pointer;
        }

        .field.button button {
            background: #26a69a;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            padding: 10px;
            width: 100%;
            border-radius: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .field.button button:hover {
            background: #00695c;
            transform: translateY(-2px);
            animation: pulse 1.2s infinite;
        }

        .field.button button::after {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.5s ease, height 0.5s ease;
        }

        .field.button button:hover::after {
            width: 150px;
            height: 150px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounceIn {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); opacity: 0.5; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 105, 92, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(0, 105, 92, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 105, 92, 0); }
        }

        /* Responsive Design */
        @media (max-width: 400px) {
            .wrapper { max-width: 90%; padding: 15px; }
            .members-list { max-height: 150px; }
            .field input[type="text"] { font-size: 0.8rem; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <section class="form">
            <header class="header-animation">Edit Group: <?= htmlspecialchars($group['group_name']) ?></header>
            <form action="update-group.php" method="POST">
                <input type="hidden" name="group_id" value="<?= $group_id ?>">
                <div class="field">
                    <label>Group Name:</label>
                    <input type="text" name="group_name" value="<?= htmlspecialchars($group['group_name']) ?>" required><br>
                </div>
                <div class="field">
                    <label>Add/Remove Members:</label><br>
                    <div class="members-list">
                        <?php while ($user = mysqli_fetch_assoc($users)) { ?>
                            <label>
                                <input type="checkbox" name="members[]" value="<?= htmlspecialchars($user['unique_id']) ?>"
                                    <?= in_array($user['unique_id'], $current_members) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?>
                            </label><br>
                        <?php } ?>
                    </div>
                </div>
                <div class="field button">
                    <button type="submit" name="update_group">Update Group</button>
                </div>
            </form>
        </section>
    </div>
</body>
</html>