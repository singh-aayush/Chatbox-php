<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit;
}
include_once "../php/config.php";

$receiver_id = $_SESSION['unique_id'];
$sql = "SELECT message_requests.*, users.fname, users.lname, users.img 
        FROM message_requests 
        LEFT JOIN users ON users.unique_id = message_requests.sender_id 
        WHERE message_requests.receiver_id = ? 
        AND message_requests.status = 'pending'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $receiver_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<?php include_once "../public/header.php"; ?>

<body>
<div class="wrapper">
  <section class="chat-area">
    <header>
      <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
      <div class="details">
        <span>Pending Chat Requests</span>
        <p>Approve or reject users who want to chat with you.</p>
      </div>
    </header>

    <div class="chat-box" style="padding: 20px;" id="requests-container">
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <div class="request-card" data-sender-id="<?php echo htmlspecialchars($row['sender_id']); ?>">
            <p><strong><?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?></strong> wants to chat with you.</p>
            <button class="btn-approve" data-action="accept">Accept</button>
            <!-- <button class="btn-reject" data-action="reject">Reject</button> -->
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No chat requests at the moment.</p>
      <?php endif; ?>
    </div>
  </section>
</div>

<style>
.request-card {
  margin-bottom: 15px;
  border-bottom: 1px solid #ccc;
  padding: 10px 0;
}
.btn-approve, .btn-reject {
  margin-right: 10px;
  padding: 6px 12px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.btn-approve { background-color: #28a745; color: white; }
.btn-reject { background-color: #dc3545; color: white; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const container = document.getElementById('requests-container');

  container.addEventListener('click', function (e) {
    if (e.target.matches('.btn-approve') || e.target.matches('.btn-reject')) {
      const card = e.target.closest('.request-card');
      const senderId = card.dataset.senderId;
      const action = e.target.dataset.action;

      fetch('../php/approve-request.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `sender_id=${encodeURIComponent(senderId)}&action=${encodeURIComponent(action)}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          card.remove();
        } else {
          alert('Failed to process request: ' + data.status);
        }
      })
      .catch(error => console.error('Error:', error));
    }
  });
});
</script>
</body>
</html>