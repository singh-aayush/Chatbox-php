function initUserChat(userId) {
    console.log("Initializing chat for user ID:", userId);

    const form = document.querySelector(".typing-area");
    const inputField = form.querySelector(".input-field");
    const sendBtn = form.querySelector("button");
    const chatBox = document.querySelector(".chat-box");

    form.onsubmit = (e) => {
        e.preventDefault();
    };

    sendBtn.onclick = () => {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "/Chat-App/php/insert-chat.php", true);
        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    inputField.value = "";
                    scrollToBottom();
                }
            }
        };
        let formData = new FormData(form);
        formData.append("outgoing_id", "<?php echo $_SESSION['unique_id']; ?>");
        formData.append("incoming_id", userId);
        xhr.send(formData);
    };

    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    setInterval(() => {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "/Chat-App/php/get-chat.php", true);
        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    chatBox.innerHTML = xhr.response;
                    scrollToBottom();
                }
            }
        };
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("outgoing_id=<?php echo $_SESSION['unique_id']; ?>&incoming_id=" + userId);
    }, 500);
}