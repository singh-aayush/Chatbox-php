<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("Location: login.php");
    exit;
}

$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

if (!$isAjax) {
    include_once "../public/header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
            background: #f0f2f5;
        }

        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #efeae2;
            background-image: url('data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" fill="%23d9d2c9" fill-opacity="0.4"/%3E%3C/svg%3E');
        }

        .chat-area .chat-header {
            padding: 15px;
            background: #00897b;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .chat-area .chat-header .back-icon {
            color: #fff;
            font-size: 20px;
            margin-right: 10px;
            cursor: pointer;
            text-decoration: none;
        }

        .chat-area .chat-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .chat-area .chat-header .details span {
            color: #fff;
            font-weight: bold;
            font-size: 16px;
        }

        .chat-area .chat-header .details p {
            color: #e0f2f1;
            font-size: 12px;
            background: rgba(255, 3, 3, 0.2); /* Optional: Matches group chat style */
        }

        .chat-area .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }

        .chat-area .typing-area {
            padding: 10px;
            background: #fff;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .chat-area .typing-area .file-input {
            display: none;
        }

        .chat-area .typing-area .insert-button {
            cursor: pointer;
            font-size: 20px;
            color: #00695c;
            margin-right: 10px;
        }

        .chat-area .typing-area .input-field {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 20px;
            background: #f0f2f5;
            font-size: 14px;
        }

        .chat-area .typing-area button {
            background: #00897b;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .chat-area .typing-area button i {
            color: #fff;
            font-size: 20px;
        }

        .text.error {
            color: #d32f2f;
            text-align: center;
            padding: 10px;
        }

        .chat {
            padding: 7px;
            background: #00897b;
            border-radius: 10px;
            max-width: 60%;
            color: #fff;
            word-break: break-word;
            white-space: normal;
            position: relative;
            margin: 5px 0;
        }

        .chat p {
            font-size: 16px;
            margin: 0;
        }

        .chat .timestamp {
            font-size: 6px !important;
            color: #e0f2f1;
            text-align: right;
            margin-top: 5px;
        }

        .chat.outgoing {
            margin-left: auto;
        }

        .chat.incoming {
            margin-right: auto;
        }

        .chat .delete-btn {
            background: rgb(255, 0, 0);
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
            display: none;
            font-size: 12px;
        }

        .chat:hover .delete-btn {
            display: block;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <section class="chat-area">
<?php } ?>
            <header class="chat-header">
                <?php
                include_once "../php/config.php";
                $user_id = isset($_GET['user_id']) ? mysqli_real_escape_string($conn, $_GET['user_id']) : null;

                if ($user_id) {
                    $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = '{$user_id}'");
                    if ($sql && mysqli_num_rows($sql) > 0) {
                        $user = mysqli_fetch_assoc($sql);
                    } else {
                        $user = ['fname' => 'Unknown', 'lname' => 'User', 'img' => 'default.jpg', 'status' => 'Offline'];
                    }
                } else {
                    $user = ['fname' => 'Invalid', 'lname' => 'User', 'img' => 'default.jpg', 'status' => 'Offline'];
                }
                ?>
                <?php if ($isAjax) { ?>
                    <!-- <a href="#" class="back-icon" onclick="document.getElementById('chat-area').innerHTML = '<div class=\"chat-placeholder\">Select a chat to start messaging</div>'; return false;"><i class="fas fa-arrow-left"></i></a> -->
                <?php } else { ?>
                    <a href="./users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
                <?php } ?>
                <img src="../php/images/<?php echo htmlspecialchars($user['img']); ?>" alt="User" />
                <div class="details">
                    <span><?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?></span>
                    <p><?php echo htmlspecialchars($user['status']); ?></p>
                </div>
            </header>
            <div class="chat-box" data-user-id="<?php echo $user_id; ?>">
                <!-- User messages will be loaded here via JS -->
            </div>
            <form action="#" class="typing-area" autocomplete="off" enctype="multipart/form-data">
                <input type="file" name="file" id="fileInput" class="file-input" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" hidden>
                <label for="fileInput" class="insert-button">
                    <i class="fas fa-paperclip"></i>
                </label>
                <input type="text" name="user_id" value="<?php echo $user_id; ?>" hidden>
                <input type="text" name="sender_id" value="<?php echo $_SESSION['unique_id']; ?>" hidden>
                <input type="text" name="message" class="input-field" placeholder="Type a message here...">
                <button><i class="fab fa-telegram-plane"></i></button>
            </form>
<?php if (!$isAjax) { ?>
        </section>
    </div>
    <script src="./js/chat.js"></script>
</body>
</html>
<?php } ?>