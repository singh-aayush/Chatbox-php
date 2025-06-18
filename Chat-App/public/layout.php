<?php
// Default values
$page_title = isset($page_title) ? htmlspecialchars($page_title) : 'My App';
$active_page = isset($active_page) ? htmlspecialchars($active_page) : '';
$content = isset($content) ? $content : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Include Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/layout.css">
</head>
<body>
    <div class='setting-container'>
        <div class="layout-header">
            <button class="back-btn" onclick="goBack()" aria-label="Go Back"><i class="bi bi-arrow-left"></i></button>
            <h1><?php echo $page_title; ?></h1>
        </div>
        <div class="content-container">
            <div class="content-scrollable" id="content-scrollable">
                <?php echo $content; ?>
            </div>
            <div class="navbar" role="navigation">
                <button onclick="window.location.href='users.php'" aria-label="Messages">
                    <div class="nav-item <?php echo $active_page === 'messages' ? 'active' : ''; ?>">
                        <i class="bi bi-chat"></i>
                        <span class="nav-text">Messages</span>
                    </div>
                </button>
                <button onclick="loadContacts()" onclick="window.location.href='contact-list.php'" aria-label="Contacts" class="create-group-btn">
                    <div class="nav-item <?php echo $active_page === 'contacts' ? 'active' : ''; ?>">
                        <i class="bi bi-person"></i>
                        <span class="nav-text">Contacts</span>
                    </div>
                </button>
                <button onclick="window.location.href='setting.php'" aria-label="Settings">
                    <div class="nav-item <?php echo $active_page === 'settings' ? 'active' : ''; ?>">
                        <i class="bi bi-gear"></i>
                        <span class="nav-text">Settings</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>