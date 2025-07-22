const chatBody = document.querySelector(".chatbot-body");
const messageInput = document.querySelector(".message-input");
const sendMessageButton = document.querySelector("#send-message");
const chatbotToggler = document.querySelector("#chatbot-toggler");
const closeChatbot = document.querySelector("#close-chatbot");

const API_KEY = "AIzaSyDzgGEt7Ouv8dIf9iYT6hUa_tdn2XCcdDc";
const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${API_KEY}`;

const userData = {
    message: null
}

const chatHistory = [];

const createMessageElement = (content, ...classes) => {
    const div = document.createElement("div");
    div.classList.add("message", ...classes);
    div.innerHTML = content;
    return div;
}

const generateBotResponse = async (incomingMessageDiv) => {
    const messageElement = incomingMessageDiv.querySelector(".text-box");
    
    chatHistory.push({
        role:"user",
        parts: [{text:userData.message}]
    });

    const requestOptions = {
        method : "POST",
        headers : {"Content-Type":"application/json"},
        body : JSON.stringify({
            contents:chatHistory
        })
    }

    try{
        const response = await fetch(API_URL,requestOptions);
        const data = await response.json();
        if(!response.ok) throw new Error(data.error.message);

        const apiResponseText = data.candidates[0].content.parts[0].text.replace(/\*\*(.*?)\*\*/g,"$1").trim();
        messageElement.innerText = apiResponseText;

        chatHistory.push({
            role:"model",
            parts: [{text:apiResponseText}]
        });

    } catch(error){
        console.log(error);
        messageElement.innerText = error.message;
        messageElement.style.color = "#ff0000";
    } finally{
        incomingMessageDiv.classList.remove("thinking");
        chatBody.scrollTo({top: chatBody.scrollHeight,behavior:"smooth"});
    }
}

const handleOutgoingMessage = (e) => {
    e.preventDefault();
    userData.message = messageInput.value.trim();
    messageInput.value = "";

    const messageContent = `<div class="text-box"><p></p></div>`;

    const outgoingMessageDiv = createMessageElement(messageContent,"user-message");
    outgoingMessageDiv.querySelector(".text-box").textContent = userData.message;
    chatBody.appendChild(outgoingMessageDiv);
    chatBody.scrollTo({top: chatBody.scrollHeight,behavior:"smooth"});

    setTimeout(() => {
        const messageContent = `<img src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png" alt="Bot Icon" class="chat-icon">
        <div class="text-box">
          <div class="thinking-indicator">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
          </div>
        </div>`;

        const incomingMessageDiv = createMessageElement(messageContent,"bot-message","thinking");
        chatBody.appendChild(incomingMessageDiv);
        chatBody.scrollTo({top: chatBody.scrollHeight,behavior:"smooth"});
        generateBotResponse(incomingMessageDiv);
    },600);
}

messageInput.addEventListener("keydown", (e) =>{
    const userMessage = e.target.value.trim();
    if(e.key === "Enter" && userMessage) {
       handleOutgoingMessage(e);
    }
});

const picker = new EmojiMart.Picker({
    theme:"light",
    skinTonePosition:"none",
    previewPosition:"none",
    onEmojiSelect:(emoji) => {
        const{selectionStart:start,selectionEnd:end } = messageInput;
        messageInput.setRangeText(emoji.native,start,end,"end");
        messageInput.focus();
    },
    onClickOutside: (e) => {
        if(e.target.id === "emoji-picker"){
            document.body.classList.toggle("show-emoji-picker");
        }
        else{
            document.body.classList.remove("show-emoji-picker");
        }
    }
});

document.querySelector(".chatbot-footer").appendChild(picker);
sendMessageButton.addEventListener("click", (e) => handleOutgoingMessage(e));
chatbotToggler.addEventListener("click",() => document.body.classList.toggle("show-chatbot"))
closeChatbot.addEventListener("click",() => document.body.classList.remove("show-chatbot"));