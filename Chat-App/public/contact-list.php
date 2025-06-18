<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit;
}
include_once "../php/config.php";

$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

// If not an AJAX request, include the full layout
if (!$isAjax) {
    $page_title = "Contacts";
    $active_page = "contacts";
    include_once "layout.php";
    exit;
}
?>

<div class="wrapper">
    <section class="users">
        <header>
            <?php
            // Fetch the logged-in user's details for the header
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = '{$_SESSION['unique_id']}'");
            if (!$sql) {
                echo "User Query Error: " . mysqli_error($conn);
                exit;
            }
            if (mysqli_num_rows($sql) > 0) {
                $row = mysqli_fetch_assoc($sql);
            } else {
                echo "No user found for unique_id: " . $_SESSION['unique_id'];
                exit;
            }
            ?>
            <div class="content">
                <img src="../php/images/<?php echo htmlspecialchars($row['img']); ?>" alt="" class="profile-img">
                <div class="details">
                    <span class="contact-name"><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></span>
                    <p class="contact-status <?php echo htmlspecialchars($row['status']) === 'Online' ? 'online' : 'offline'; ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </p>
                </div>
            </div>
            <div class="contact-list">
            <?php
            // Fetch all users except the logged-in user
            $sql_all = mysqli_query($conn, "SELECT * FROM users WHERE unique_id != '{$_SESSION['unique_id']}'");
            if (!$sql_all) {
                echo "Contacts Query Error: " . mysqli_error($conn);
                exit;
            }
            if (mysqli_num_rows($sql_all) > 0) {
                $first = true;
                while ($contact = mysqli_fetch_assoc($sql_all)) {
                    // Skip the hr before the first contact
                    if (!$first) {
                        echo '<hr>';
                    }
                    $first = false;
            ?>
                <a href="chat.php?user_id=<?php echo htmlspecialchars($contact['unique_id']); ?>" class="contact-item">
                    <img src="../php/images/<?php echo htmlspecialchars($contact['img']); ?>" alt="" class="profile-img">
                    <div class="contact-details">
                        <span class="contact-name"><?php echo htmlspecialchars($contact['fname'] . " " . $row['lname']); ?></span>
                        <p class="contact-status <?php echo htmlspecialchars($contact['status']) === 'Online' ? 'online' : 'offline'; ?>">
                            <?php echo htmlspecialchars($contact['status']); ?>
                        </p>
                    </div>
                </a>
            <?php
                }
            } else {
                echo '<p class="no-users">No contacts found</p>';
            }
            ?>
        </div>
        </header>
        
    </section>
</div>

<style>
    .content {
        padding: 20px 0;
        display: flex;
        align-items: center;
        gap: 15px; /* Space between image and name */
        border-bottom: 1px solid gray;
    }
    .content img{
        border: 1px solid black;
    }
    .profile-img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    .contact-name {
        font-weight: bold;
        font-size: 16px;
    }
    .contact-status {
        margin: 5px 0;
        font-size: 12px;
    }
    .contact-status.active {
        color: #28a745; /* Green for Active */
    }
    .contact-status.offline {
        color: #6c757d; /* Gray for Offline */
    }
    .users {
        padding: 1rem 1.5rem;
    }
    .contact-list {
        padding: 20px 0;
        background-color: #fff;
        border-radius: 15px;
        /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
        /* margin: 20px; */
    }

    .contact-list a{
        padding:0;
    }
    .contact-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 10px;
        text-decoration: none;
        color: #000;
        border-bottom: none; /* Remove default border since we're using hr */
    }

    .contact-item img{
        border: 1px solid black;
    }
    .contact-item:hover {
        background-color: #f1f1f1;
    }
    .contact-details {
        display: flex;
        flex-direction: column;
    }
    .contact-name {
        font-weight: bold;
        font-size: 16px;
    }
    .contact-status {
        font-size: 12px;
        color: #666;
    }
    .contact-status.online {
        color: #28a745;
    }
    .contact-status.offline {
        color: #6c757d;
    }
    .no-users {
        text-align: center;
        color: #666;
        padding: 20px;
    }
    .contact-list hr {
        border: 0;
        border-top: 1px solid #ccc;
        margin: 10px 0;
    }
    
@media (max-width: 426px) {
    .users{
        padding: 0;
    }
}
</style>