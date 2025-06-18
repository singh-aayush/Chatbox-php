<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit;
}

// Include configuration and verify database connection
include_once "../php/config.php";
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>

<?php include_once "header.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        html, body {
            height: 100vh;
            overflow: hidden;
        }

        body {
            display: flex;
            background: #f0f2f5;
        }

        .wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
            margin: 0 auto;
            background: #fff;
        }

        .sidebar {
            width: 30%;
            max-width: 350px;
            height: 100vh;
            background: #e0f2f1;
            border-right: 1px solid #ccc;
            display: flex;
            flex-direction: column;
        }

        .chat-area {
            flex: 1;
            width: 70%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: #efeae2;
            background-image: url('data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" fill="%23d9d2c9" fill-opacity="0.4"/%3E%3C/svg%3E');
        }

        .header {
            padding: 15px;
            background: #00897b;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .content {
            display: flex;
            align-items: center;
        }

        .content img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .details span {
            color: black;
            font-weight: bold;
            font-size: 16px;
            padding-bottom: 5px;
        }

        .details p {
            color: black;
            font-size: 12px;
            padding: 3px;
        }

        .logout {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 5px;
            background: #00695c;
        }

        .logout:hover {
            background: #004d40;
        }

        .nav-icons {
            padding: 10px;
            display: flex;
            justify-content: space-around;
            background: #b2dfdb;
            flex-shrink: 0;
        }

        .nav-icons i {
            font-size: 20px;
            color: #00695c;
            cursor: pointer;
        }

        .nav-icons i:hover {
            color: #004d40;
        }

        .search {
            padding: 10px;
            display: flex;
            background: #fff;
            flex-shrink: 0;
        }

        .search input {
            width: calc(100% - 50px);
            padding: 8px;
            border: none;
            border-radius: 20px;
            background: #f0f2f5;
            font-size: 14px;
        }

        .search button {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            background: #00897b;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 10px;
        }

        .users, .group-list {
            overflow-y: auto;
            flex: 1;
        }

        .group-item, .user-item {
            padding: 10px 15px;
            border-bottom: 1px solid #e0e0e0;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .group-item:hover, .users-list a:hover {
            background: #b2dfdb;
        }

        .users-list div {
            color: black !important;
            text-decoration: none;
            margin: 10px 10px;
        }

        .group-item a, .users-list a {
            text-decoration: none !important;
            color: black !important;
            font-weight: bold;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .users-list div img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-left: 20px;
        }

        .users-list .status-dot {
            margin-left: auto;
            font-size: 12px;
            color: green;
        }

        .users-list .status-dot.offline {
            color: green;
        }

        .chat-area .chat-header {
            padding: 15px;
            background: #00897b;
            display: flex;
            align-items: center;
            flex-shrink: 0;
            width: 100%;
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
        }

        .chat-area .chat-header .edit-group-btn {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 5px;
            background: #00695c;
            margin-left: 10px;
        }

        .chat-area .chat-header .edit-group-btn:hover {
            background: #004d40;
        }

        .chat-area .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            width: 100%;
        }

        .chat-area .typing-area {
            padding: 10px;
            background: #fff;
            display: flex;
            align-items: center;
            flex-shrink: 0;
            width: 100%;
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

        .chat-placeholder {
            color: #666;
            font-size: 18px;
            text-align: center;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .text.error {
            color: #d32f2f;
            text-align: center;
            padding: 10px;
        }

        .chat {
            padding: 7px;
            background: rgba(0, 137, 123, 0.47);
            border-radius: 10px;
            max-width: 30%;
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
        <section class="sidebar">
            <header class="header">
                <?php
                $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = '" . mysqli_real_escape_string($conn, $_SESSION['unique_id']) . "'");
                if (!$sql) {
                    error_log("User query failed: " . mysqli_error($conn));
                    echo '<div class="text error">Database error: Unable to fetch user data</div>';
                    exit;
                }
                if (mysqli_num_rows($sql) > 0) {
                    $row = mysqli_fetch_assoc($sql);
                } else {
                    echo '<div class="text error">User not found</div>';
                    exit;
                }
                ?>
                <div class="content">
                    <img src="../php/images/<?php echo htmlspecialchars($row['img']); ?>" alt="Profile Image">
                    <div class="details">
                        <span><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></span>
                        <p><?php echo htmlspecialchars($row['status']); ?></p>
                    </div>
                </div>
                <a href="setting.php" class="logout">Settings</a>
            </header>
            <div class="nav-icons">
                <i class="fas fa-comments" title="Current Chats"></i>
                <i class="fas fa-address-book" title="Contacts" onclick="window.location.href='setting.php'"></i>
                <i class="fas fa-users" title="Create Group" onclick="window.location.href='create-group.php'"></i>
            </div>
            <div class="users">
                <div class="search">
                    <input type="text" placeholder="Search users or groups...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <div class="users-list">
                    <!-- Users will be populated by JavaScript -->
                </div>
            </div>
            <div class="group-list">
                <?php
                $unique_id = mysqli_real_escape_string($conn, $_SESSION['unique_id']);
                $group_query = mysqli_query($conn, 
                    "SELECT g.group_id, g.group_name, g.group_image 
                     FROM `groups` g 
                     INNER JOIN group_members gm ON g.group_id = gm.group_id 
                     WHERE gm.unique_id = '$unique_id'");
                if (!$group_query) {
                    error_log("Group query failed: " . mysqli_error($conn));
                    echo '<div class="group-item text error">Database error: Unable to fetch groups - ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
                } elseif (mysqli_num_rows($group_query) > 0) {
                    while ($group = mysqli_fetch_assoc($group_query)) {
                        $group_image = $group['group_image'] ? htmlspecialchars($group['group_image']) : '../php/images/default-group.png';
                        $member_count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM group_members WHERE group_id = " . (int)$group['group_id']);
                        $member_count = $member_count_query ? mysqli_fetch_assoc($member_count_query)['count'] : 0;
                        echo '<div class="group-item" data-group-id="' . (int)$group['group_id'] . '">';
                        echo '<a href="#" onclick="loadGroupChat(' . (int)$group['group_id'] . '); return false;" class="group-link">';
                        echo '<img src="' . $group_image . '" alt="' . htmlspecialchars($group['group_name']) . '" style="width: 35px; height: 35px; border-radius: 50%; margin-right: 10px;">';
                        echo '<span>' . htmlspecialchars($group['group_name']) . '</span>';
                        echo '<span class="status-dot" style="margin-left: auto; color: #26a69a;">' . $member_count . ' members</span>';
                        echo '</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="group-item">No groups yet.</div>';
                }
                ?>
            </div>
        </section>
        <section class="chat-area" id="chat-area">
            <div class="chat-placeholder">Select a chat to start messaging</div>
        </section>
    </div>
    <script>
        function loadGroupChat(groupId) {
            const chatArea = document.getElementById('chat-area');
            chatArea.innerHTML = '<div class="chat-placeholder">Loading...</div>';

            const xhr = new XMLHttpRequest();
            xhr.open('GET', `group-chat.php?group_id=${groupId}&ajax=1`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    chatArea.innerHTML = xhr.responseText;
                    if (typeof initGroupChat === 'function') {
                        initGroupChat(groupId);
                    } else {
                        console.error('initGroupChat function not found. Retrying script load...');
                        const script = document.createElement('script');
                        script.src = 'js/group-chat.js?' + new Date().getTime();
                        script.onload = () => {
                            if (typeof initGroupChat === 'function') {
                                initGroupChat(groupId);
                            } else {
                                console.error('initGroupChat still not found after reload');
                                chatArea.innerHTML = '<div class="chat-placeholder">Error: Chat script failed to load</div>';
                            }
                        };
                        script.onerror = () => {
                            console.error('Failed to load group-chat.js');
                            chatArea.innerHTML = '<div class="chat-placeholder">Error: Chat script not found</div>';
                        };
                        document.body.appendChild(script);
                    }
                } else {
                    console.error('Error loading chat:', xhr.status, xhr.responseText);
                    chatArea.innerHTML = '<div class="chat-placeholder">Error loading chat</div>';
                }
            };
            xhr.onerror = () => {
                console.error('Network error loading chat');
                chatArea.innerHTML = '<div class="chat-placeholder">Error: Network issue</div>';
            };
            xhr.send();
        }

        function loadUserChat(userId) {
            const chatArea = document.getElementById('chat-area');
            chatArea.innerHTML = '<div class="chat-placeholder">Loading...</div>';

            const xhr = new XMLHttpRequest();
            xhr.open('GET', `chat.php?user_id=${userId}&ajax=1`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    chatArea.innerHTML = xhr.responseText;
                    if (typeof initUserChat === 'function') {
                        initUserChat(userId);
                    } else {
                        console.error('initUserChat function not found. Retrying script load...');
                        const script = document.createElement('script');
                        script.src = 'js/user-chat.js?' + new Date().getTime();
                        script.onload = () => {
                            if (typeof initUserChat === 'function') {
                                initUserChat(userId);
                            } else {
                                console.error('initUserChat still not found after reload');
                                chatArea.innerHTML = '<div class="chat-placeholder">Error: Chat script failed to load</div>';
                            }
                        };
                        script.onerror = () => {
                            console.error('Failed to load user-chat.js');
                            chatArea.innerHTML = '<div class="chat-placeholder">Error: Chat script not found</div>';
                        };
                        document.body.appendChild(script);
                    }
                } else {
                    console.error('Error loading chat:', xhr.status, xhr.responseText);
                    chatArea.innerHTML = '<div class="chat-placeholder">Error loading chat</div>';
                }
            };
            xhr.onerror = () => {
                console.error('Network error loading chat');
                chatArea.innerHTML = '<div class="chat-placeholder">Error: Network issue</div>';
            };
            xhr.send();
        }
    </script>
    <script src="js/users.js"></script>
    <script src="js/group-chat.js"></script>
</body>
</html>