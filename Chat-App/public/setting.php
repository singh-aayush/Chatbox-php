<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session to access logged-in user data
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['unique_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include_once "../php/config.php";

// Check if database connection is established
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . ($conn ? $conn->connect_error : "Configuration file not found"));
}

// Set page variables
$page_title = "Settings";
$active_page = "settings";

// Fetch user data from database
$sql = mysqli_query($conn, "SELECT fname, lname, img, unique_id FROM users WHERE unique_id = '{$_SESSION['unique_id']}'");
if (!$sql) {
    echo "User Query Error: " . mysqli_error($conn);
    exit();
}
if (mysqli_num_rows($sql) > 0) {
    $row = mysqli_fetch_assoc($sql);
    $user = [
        "name" => $row['fname'] . " " . $row['lname'],
        "role" => "Employee",
        "image" => "../php/images/" . ($row['img'] ?: 'default.jpeg'),
        "unique_id" => $row['unique_id']
    ];
} else {
    header("Location: login.php");
    exit();
}

// Close database connection
mysqli_close($conn);

// Capture the page-specific content
ob_start();
?>

<link rel="stylesheet" href="css/setting.css">

<div class="layout-setting-container">
    <div class="profile-section">
        <img src="<?php echo htmlspecialchars($user['image']); ?>" alt="Profile" class="profile-img">
        <div class="profile-info">
            <span class="profile-name"><?php echo htmlspecialchars($user['name']); ?></span>
            <span class="profile-role"><?php echo htmlspecialchars($user['role']); ?></span>
        </div>
    </div>

    <div class="settings-list">
        <a href="edit_profile.php" class="settings-link">
            <div class="settings-item">
                <i class="bi bi-pencil"></i>
                <span>Edit Profile</span>
            </div>
        </a>

        <div class="settings-item" onclick="window.location.href='users.php'" role="button" tabindex="0" aria-label="Change Password">
            <i class="bi bi-chat"></i>
            <span>Send</span>
        </div>
        <div class="settings-item help clickable" onclick="openPopup()" role="button" tabindex="0" aria-label="Open Help Popup">
            <i class="bi bi-question-circle"></i>
            <span>Help</span>
        </div>
        <a href="../php/logout.php?logout_id=<?php echo htmlspecialchars($user['unique_id']); ?>" class="logout">
            <div class="settings-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </div>
        </a>
    </div>

    <!-- Popup Overlay -->
    <div class="popup-overlay" id="helpPopup" role="dialog" aria-labelledby="helpPopupTitle" aria-modal="true">
        <div class="popup-content">
            <button class="popup-close" onclick="closePopup()" aria-label="Close Help Popup">×</button>
            <div class="wrapper">
                <h2 id="helpPopupTitle">Help & Support</h2>
                <form action="../php/send-ticket.php" method="POST" enctype="multipart/form-data">
                    <input type="text" name="subject" placeholder="Subject" required aria-required="true" />
                    <textarea name="message" placeholder="Describe your issue..." required aria-required="true"></textarea>
                    <input type="file" name="attachment" accept=".jpg,.png,.pdf,.docx" />
                    <button type="submit">Send Ticket</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Ensure popup is hidden on page load
    document.addEventListener('DOMContentLoaded', function() {
        const popup = document.getElementById('helpPopup');
        popup.style.display = 'none';
        popup.classList.remove('hide'); // Remove any hide class if present
    });

    function openPopup() {
        const popup = document.getElementById('helpPopup');
        popup.style.display = 'flex';
        popup.focus(); // Focus on popup for accessibility
    }

    function closePopup() {
        const popup = document.getElementById('helpPopup');
        popup.classList.add('hide');
        setTimeout(() => {
            popup.style.display = 'none';
            popup.classList.remove('hide');
        }, 300);
    }

    // Close popup when clicking outside the popup content
    document.getElementById('helpPopup').addEventListener('click', function(event) {
        if (event.target === this) {
            closePopup();
        }
    });

    // Close popup with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePopup();
        }
    });

    // Client-side form validation
    document.querySelector('form').addEventListener('submit', function(event) {
        const subject = document.querySelector('input[name="subject"]').value.trim();
        const message = document.querySelector('textarea[name="message"]').value.trim();
        if (!subject || !message) {
            event.preventDefault();
            alert('Please fill out both the subject and message fields.');
        }
    });
</script>

<style>
/* Popup Overlay */
.popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

/* Help item cursor */
.help.clickable {
    cursor: pointer;
}

/* Popup Content */
.popup-content {
    background: #fff;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
    padding: 20px;
    position: relative;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    animation: fadeIn 0.3s ease-in-out;
}

/* Close Button */
.popup-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ff4d4d;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.popup-close:hover {
    background: #e60000;
}

/* Form Wrapper */
.wrapper {
    padding: 20px;
    text-align: center;
}

.wrapper h2 {
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
}

/* Form Inputs */
.wrapper input[type="text"],
.wrapper textarea,
.wrapper input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

.wrapper textarea {
    height: 150px;
    resize: vertical;
}

/* Submit Button */
.wrapper button {
    background: #28a745;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}

.wrapper button:hover {
    background: #218838;
}

/* Animation for Popup */
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

@keyframes fadeOut {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.9); }
}

.popup-overlay.hide {
    animation: fadeOut 0.3s ease-in-out forwards;
}

/* Responsive Design */
@media (max-width: 600px) {
    .popup-content {
        width: 95%;
        padding: 15px;
    }

    .wrapper h2 {
        font-size: 20px;
    }

    .wrapper button {
        font-size: 14px;
        padding: 8px 16px;
    }
}

@media (max-width: 426px) {
    .layout-setting-container {
        padding: 0 5px;
    }

    .profile-section .profile-img{
        margin-right: 1rem;
    }
}


</style>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>