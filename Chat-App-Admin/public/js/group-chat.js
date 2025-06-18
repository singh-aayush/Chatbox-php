const form = document.querySelector(".typing-area"),
      inputField = form.querySelector(".input-field"),
      sendBtn = form.querySelector("button"),
      fileInput = form.querySelector("input[type='file']"),
      chatBox = document.querySelector(".chat-box");

const groupIdInput = form.querySelector("input[name='group_id']");
const senderIdInput = form.querySelector("input[name='sender_id']");

// Handle file selection
fileInput.addEventListener('change', function(e) {
    if (this.files.length > 0) {
        // Automatically submit form when file is selected
        form.dispatchEvent(new Event('submit'));
    }
});

// Get values safely
function getGroupId() {
  return groupIdInput ? groupIdInput.value : null;
}

function getSenderId() {
  return senderIdInput ? senderIdInput.value : null;
}

// Modify the form submit handler:
form.onsubmit = (e) => {
  e.preventDefault();
  
  const hasFile = fileInput.files.length > 0;
  const hasMessage = inputField.value.trim().length > 0;
  
  if (!hasFile && !hasMessage) return;

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../php/insert-group-message.php", true);

  xhr.onerror = () => {
    console.error("Upload failed:", xhr.statusText);
  };

  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        inputField.value = "";
        fileInput.value = "";
        scrollToBottom();
      } else {
        console.error("Server error:", xhr.status);
      }
    }
  };
  
  const formData = new FormData(form);
  xhr.send(formData);
};

// Auto-load group messages every 500ms
setInterval(() => {
  const groupId = getGroupId();
  if (!groupId) return; // Don't fetch if group ID is invalid

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../php/get-group-messages.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      if (xhr.responseText.startsWith("Error:")) {
        chatBox.innerHTML = `<p class='text error'>${xhr.responseText}</p>`;
      } else {
        chatBox.innerHTML = xhr.responseText;
        scrollToBottom();
      }
    }
  };
  const formData = new FormData();
  formData.append("group_id", groupId);
  xhr.send(formData);
}, 500);

// Scroll on hover disable
chatBox.onmouseenter = () => chatBox.classList.add("active");
chatBox.onmouseleave = () => chatBox.classList.remove("active");

// Scroll to bottom if not hovered
function scrollToBottom() {
  if (!chatBox.classList.contains("active")) {
    chatBox.scrollTop = chatBox.scrollHeight;
  }
}

chatBox.addEventListener("click", function (e) {
    if (e.target.classList.contains("delete-btn")) {
        const msgId = e.target.getAttribute("data-id");

        if (confirm("Are you sure you want to delete this message?")) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../php/delete-group-msg.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200 && xhr.responseText.trim() === "success") {
                    e.target.closest(".chat").remove();
                } else {
                    console.error("Server Error:", xhr.responseText);
                    alert("Failed to delete message.");
                }
            };
            xhr.send("message_id=" + encodeURIComponent(msgId));
        }
    }
});


