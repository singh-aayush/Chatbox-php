<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit;
}
include_once "../php/config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Group</title>
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
        .field input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 2px solid #e0f2f1;
            border-radius: 20px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .field input[type="text"]:focus,
        .field input[type="file"]:focus {
            border-color: #00695c;
            box-shadow: 0 0 5px rgba(0, 105, 92, 0.2);
        }

        .field input[type="file"] {
            padding: 8px;
            cursor: pointer;
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

        .field.button input {
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

        .field.button input:hover {
            background: #00695c;
            transform: translateY(-2px);
            animation: pulse 1.2s infinite;
        }

        .field.button input::after {
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

        .field.button input:hover::after {
            width: 150px;
            height: 150px;
        }

        .success-message {
            color: green;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid green;
            border-radius: 5px;
            text-align: center;
        }

        .error-message {
            color: red;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid red;
            border-radius: 5px;
            text-align: center;
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
            .field input[type="text"], .field input[type="file"] { font-size: 0.8rem; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <section class="form">
            <header class="header-animation">Create Group</header>
            <?php
            if (isset($_GET['success'])) {
                echo '<div class="success-message">' . htmlspecialchars($_GET['success']) . '</div>';
            } elseif (isset($_GET['error'])) {
                echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            ?>
            <form action="../php/create-group.php" method="POST" enctype="multipart/form-data">
                <div class="field">
                    <label>Group Name:</label>
                    <input type="text" name="group_name" required>
                </div>
                <div class="field">
                    <label>Group Image (Optional):</label>
                    <input type="file" name="group_image" accept="image/*">
                </div>
                <div class="field">
                    <label>Select Members:</label>
                    <div class="members-list">
                        <?php
                        $user_id = mysqli_real_escape_string($conn, $_SESSION['unique_id']);
                        $query = mysqli_query($conn, "SELECT * FROM users WHERE unique_id != '$user_id'");
                        if (!$query) {
                            echo '<div class="error-message">Database error: Unable to fetch users</div>';
                        } elseif (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<label><input type="checkbox" name="members[]" value="' . htmlspecialchars($row['unique_id']) . '"> ' . htmlspecialchars($row['fname'] . ' ' . $row['lname']) . '</label><br>';
                            }
                        } else {
                            echo '<div class="error-message">No users available to add</div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="field button">
                    <input type="submit" value="Create Group">
                </div>
            </form>
        </section>
    </div>
</body>
</html>