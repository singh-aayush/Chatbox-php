const form = document.querySelector(".typing-area"),
      inputField = form.querySelector(".input-field"),
      fileInput = form.querySelector(".file-input"),
      sendBtn = form.querySelector("button"),
      chatBox = document.querySelector(".chat-box");

form.onsubmit = (e) => {
    e.preventDefault(); // Prevent form from submitting normally
};

chatBox.addEventListener("click", function (e) {
    if (e.target.classList.contains("delete-btn")) {
        const msgId = e.target.getAttribute("data-id");

        if (confirm("Delete this message?")) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../php/delete-msg.php", true);
            xhr.onload = () => {
                if (xhr.status === 200) {
                    console.log(xhr.response);
                }
            };
            const formData = new FormData();
            formData.append("msg_id", msgId);
            xhr.send(formData);
        }
    }
});


sendBtn.onclick = ()=> {
    
   // AJAX Code
   let xhr = new XMLHttpRequest();
   xhr.open("POST", "../php/insert-chat.php", true);
   xhr.onload = ()=>{
       if(xhr.readyState === XMLHttpRequest.DONE){
           if(xhr.status === 200){
               inputField.value = ""; // clear input field
               scrollToBottom();

               // Clear file input after sending
               fileInput.value = "";

           }
       }
   }
   let formData = new FormData(form); // new formdata object
   xhr.send(formData);
}


// ChatBox hover state (fix: mouseleave also needed)
chatBox.onmouseenter = () => {
    chatBox.classList.add("active");
};
chatBox.onmouseleave = () => {
    chatBox.classList.remove("active");
};

// Periodic message fetching
setInterval(() => {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/get-chat.php", true);

    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            let data = xhr.response;
            chatBox.innerHTML = data;

            if (!chatBox.classList.contains("active")) {
                scrollToBottom();
            }
        }
    };

    
    const formData = new FormData();
    formData.append("outgoing_id", form.querySelector("[name='outgoing_id']").value);
    formData.append("incoming_id", form.querySelector("[name='incoming_id']").value);
    
    xhr.send(formData);
}, 500);

function scrollToBottom() {
    chatBox.scrollTop = chatBox.scrollHeight;
}
