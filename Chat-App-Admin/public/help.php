<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit;
}
?>

<?php include_once "../public/header.php"; ?>
<body>
  <div class="wrapper">
    <h2>Help & Support</h2>
    <form action="../php/send-ticket.php" method="POST" enctype="multipart/form-data">
      <input type="text" name="subject" placeholder="Subject" required />
      <textarea name="message" placeholder="Describe your issue..." required></textarea>
      <input type="file" name="attachment" accept=".jpg,.png,.pdf,.docx" />
      <button type="submit">Send Ticket</button>
    </form>
  </div>
</body>
</html>
