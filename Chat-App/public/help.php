<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit;
}
?>

<?php include_once "../public/header.php"; ?>
<body>
  <!-- Popup Overlay -->
  <div class="popup-overlay" id="helpPopup">
    <div class="popup-content">
      <!-- Close Button -->
      <button class="popup-close" onclick="closePopup()">&times;</button>
      <div class="wrapper">
        <h2>Help & Support</h2>
        <form action="../php/send-ticket.php" method="POST" enctype="multipart/form-data">
          <input type="text" name="subject" placeholder="Subject" required />
          <textarea name="message" placeholder="Describe your issue..." required></textarea>
          <input type="file" name="attachment" accept=".jpg,.png,.pdf,.docx" />
          <button type="submit">Send Ticket</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Function to close the popup
    function closePopup() {
      document.getElementById('helpPopup').style.display = 'none';
    }
  </script>
</body>
</html>

<style>
/* Popup Overlay */
.popup-overlay {
  display: none; /* Hidden by default */
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
  z-index: 1000;
  justify-content: center;
  align-items: center;
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
</style>