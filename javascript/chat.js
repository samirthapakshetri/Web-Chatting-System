const form = document.querySelector(".typing-area"),
inputField = form.querySelector(".input-field"),
fileInput = form.querySelector("#file-upload"),
sendBtn = form.querySelector("button"),
chatBox = document.querySelector(".chat-box");

form.onsubmit = (e) => {
  e.preventDefault();
}

sendBtn.onclick = () => {
  let formData = new FormData(form);
  
  // Only proceed if there's a message or a file
  if (inputField.value.trim() !== "" || fileInput.files.length > 0) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/insert-chat.php", true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          inputField.value = "";
          // Reset file input and hide preview
          fileInput.value = "";
          document.getElementById('file-preview').classList.add('hidden');
          document.getElementById('file-name').textContent = "";
          
          // Scroll to bottom of chat
          scrollToBottom();
        }
      }
    }
    xhr.send(formData);
  }
}

chatBox.onmouseenter = () => {
  chatBox.classList.add("active");
}

chatBox.onmouseleave = () => {
  chatBox.classList.remove("active");
}

function scrollToBottom() {
  chatBox.scrollTop = chatBox.scrollHeight;
}

setInterval(() => {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/get-chat.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response;
        chatBox.innerHTML = data;
        if (!chatBox.classList.contains("active")) {
          scrollToBottom();
        }
      }
    }
  }
  let formData = new FormData(form);
  xhr.send(formData);
}, 500);