console.log('group-chat.js loaded');

let chatPollInterval = null;

// Prevent redefinition of initGroupChat
if (!window.initGroupChat) {
    function initGroupChat(groupId) {
        console.log(`Initializing chat for group_id: ${groupId}`);

        // Clear any existing polling interval
        if (chatPollInterval) {
            clearInterval(chatPollInterval);
            chatPollInterval = null;
        }

        // Query DOM elements
        const form = document.querySelector(".typing-area");
        const inputField = form ? form.querySelector(".input-field") : null;
        const sendBtn = form ? form.querySelector("button") : null;
        const fileInput = form ? form.querySelector("input[type='file']") : null;
        const chatBox = document.querySelector(".chat-box");
        const groupIdInput = form ? form.querySelector("input[name='group_id']") : null;
        const senderIdInput = form ? form.querySelector("input[name='sender_id']") : null;

        // Validate DOM elements
        if (!form || !inputField || !sendBtn || !fileInput || !chatBox || !groupIdInput || !senderIdInput) {
            console.error("Required DOM elements not found:", { form, inputField, sendBtn, fileInput, chatBox, groupIdInput, senderIdInput });
            if (chatBox) {
                chatBox.innerHTML = "<p class='text error'>Error: Chat interface not loaded correctly</p>";
            }
            return;
        }

        // Validate group ID
        if (groupIdInput.value != groupId) {
            console.error(`Group ID mismatch: expected ${groupId}, got ${groupIdInput.value}`);
            chatBox.innerHTML = "<p class='text error'>Error: Invalid group ID</p>";
            return;
        }

        // Get values safely
        function getGroupId() {
            return groupIdInput ? groupIdInput.value : null;
        }

        function getSenderId() {
            return senderIdInput ? senderIdInput.value : null;
        }

        // Remove existing event listeners to prevent duplication
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
        const newFileInput = newForm.querySelector("input[type='file']");
        const newInputField = newForm.querySelector(".input-field");

        // Handle file selection
        function setupFileInputListener() {
            newFileInput.addEventListener('change', function(e) {
                console.log("File selected:", this.files);
                if (this.files.length > 0) {
                    newForm.dispatchEvent(new Event('submit'));
                }
            });
        }

        // Form submit handler
        function setupFormSubmitListener() {
            newForm.addEventListener('submit', (e) => {
                e.preventDefault();
                console.log("Form submitted for group_id:", getGroupId());

                const hasFile = newFileInput.files.length > 0;
                const hasMessage = newInputField.value.trim().length > 0;

                if (!hasFile && !hasMessage) {
                    console.log("No message or file to send");
                    return;
                }

                const xhr = new XMLHttpRequest();
                xhr.open("POST", "../php/insert-group-message.php", true);

                xhr.onerror = () => {
                    console.error("Upload failed:", xhr.statusText);
                    chatBox.innerHTML = "<p class='text error'>Error: Failed to send message</p>";
                };

                xhr.onload = () => {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            console.log("Message sent successfully");
                            newInputField.value = "";
                            newFileInput.value = "";
                            scrollToBottom();
                        } else {
                            console.error("Server error:", xhr.status, xhr.responseText);
                            chatBox.innerHTML = "<p class='text error'>Error: Server error while sending message</p>";
                        }
                    }
                };

                const formData = new FormData(newForm);
                xhr.send(formData);
            });
        }

        // Fetch group messages
        function fetchMessages() {
            const groupId = getGroupId();
            if (!groupId) {
                console.error("No group ID found");
                chatBox.innerHTML = "<p class='text error'>Error: No group ID</p>";
                return;
            }

            console.log(`Fetching messages for group_id: ${groupId}`);
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../php/get-group-messages.php", true);
            xhr.onload = () => {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    if (xhr.responseText.startsWith("Error:")) {
                        console.error("Server error response:", xhr.responseText);
                        chatBox.innerHTML = `<p class='text error'>${xhr.responseText}</p>`;
                    } else {
                        chatBox.innerHTML = xhr.responseText;
                        scrollToBottom();
                    }
                } else {
                    console.error("Fetch messages error:", xhr.status, xhr.responseText);
                }
            };
            xhr.onerror = () => {
                console.error("Network error fetching messages");
            };
            const formData = new FormData();
            formData.append("group_id", groupId);
            xhr.send(formData);
        }

        // Setup event listeners
        setupFileInputListener();
        setupFormSubmitListener();

        // Initial fetch
        fetchMessages();

        // Auto-load group messages every 2000ms
        chatPollInterval = setInterval(fetchMessages, 2000);

        // Scroll on hover disable
        chatBox.onmouseenter = () => chatBox.classList.add("active");
        chatBox.onmouseleave = () => chatBox.classList.remove("active");

        // Scroll to bottom if not hovered
        function scrollToBottom() {
            if (!chatBox.classList.contains("active")) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }

        // Handle message deletion
        function setupDeleteListener() {
            chatBox.addEventListener("click", function (e) {
                if (e.target.classList.contains("delete-btn")) {
                    const msgId = e.target.getAttribute("data-id");
                    console.log("Delete button clicked for message_id:", msgId);

                    if (confirm("Are you sure you want to delete this message?")) {
                        const xhr = new XMLHttpRequest();
                        xhr.open("POST", "../php/delete-group-msg.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onload = function () {
                            if (xhr.status === 200 && xhr.responseText.trim() === "success") {
                                console.log("Message deleted:", msgId);
                                e.target.closest(".chat").remove();
                            } else {
                                console.error("Server Error:", xhr.responseText);
                                alert("Failed to delete message.");
                            }
                        };
                        xhr.onerror = () => {
                            console.error("Network error deleting message");
                        };
                        xhr.send("message_id=" + encodeURIComponent(msgId));
                    }
                }
            });
        }

        setupDeleteListener();
    }

    // Expose initGroupChat globally
    window.initGroupChat = initGroupChat;
}