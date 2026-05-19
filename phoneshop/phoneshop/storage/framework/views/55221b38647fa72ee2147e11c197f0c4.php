<style>
#liveChatWidget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1050;
    font-family: 'Inter', system-ui, sans-serif;
}
#chatToggle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    font-size: 1.4rem;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}
#chatToggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
}
#chatPopup {
    display: none;
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 350px;
    background: var(--bs-body-bg, #fff);
    border-radius: 1rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    overflow: hidden;
    animation: slideUp 0.3s ease;
}
#chatPopup.open { display: block; }

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.25rem;
}
.chat-header h6 { margin: 0; font-weight: 600; }
.chat-header small { opacity: 0.8; }
.chat-body {
    height: 300px;
    overflow-y: auto;
    padding: 1rem;
}
.chat-msg {
    margin-bottom: 0.75rem;
    max-width: 85%;
    padding: 0.6rem 0.9rem;
    border-radius: 0.75rem;
    word-wrap: break-word;
    font-size: 0.9rem;
    line-height: 1.4;
}
.chat-msg.customer {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-left-radius: 0.2rem;
    margin-right: auto;
}
.chat-msg.admin {
    background: var(--bs-tertiary-bg, #f1f3f5);
    color: var(--text-dark, #1e293b);
    border-bottom-right-radius: 0.2rem;
    margin-left: auto;
}
.chat-msg small {
    display: block;
    font-size: 0.65rem;
    opacity: 0.7;
    margin-top: 0.2rem;
}
.chat-input-area {
    border-top: 1px solid var(--bs-border-color, #dee2e6);
    padding: 0.75rem;
    display: flex;
    gap: 0.5rem;
}
.chat-input-area input {
    flex: 1;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    outline: none;
}
.chat-input-area input:focus {
    border-color: #667eea;
}
.chat-input-area button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
}
.chat-input-area button:hover { opacity: 0.9; }
.chat-start-screen {
    text-align: center;
    padding: 2rem 1rem;
}
.chat-start-screen input {
    width: 100%;
    margin-bottom: 0.75rem;
}
.unread-badge-chat {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}
</style>

<div id="liveChatWidget">
    <button id="chatToggle" onclick="toggleChat()" title="Live Chat">
        <i class="fa-solid fa-comment-dots"></i>
    </button>

    <div id="chatPopup">
        <div class="chat-header d-flex justify-content-between align-items-center">
            <div>
                <h6><i class="fa-solid fa-comment me-2"></i>Live Chat</h6>
                <small>We typically reply in minutes</small>
            </div>
            <button class="btn btn-sm text-white" onclick="toggleChat()" style="opacity:0.8">&times;</button>
        </div>

        <div id="chatBody" class="chat-body">
            <div id="chatStartScreen" class="chat-start-screen">
                <i class="fa-solid fa-comment-dots fa-3x text-primary mb-3 opacity-50"></i>
                <p class="text-muted mb-3">Start a conversation with us!</p>
                <input type="text" id="customerName" class="form-control" placeholder="Your name *" required>
                <input type="email" id="customerEmail" class="form-control" placeholder="Your email (optional)">
                <button class="btn btn-primary w-100" onclick="startChat()">
                    <i class="fa-solid fa-play me-1"></i>Start Chat
                </button>
            </div>
            <div id="chatMessages" style="display:none;"></div>
        </div>

        <div id="chatInputArea" class="chat-input-area" style="display:none;">
            <input type="text" id="chatMessageInput" placeholder="Type a message..." maxlength="1000" onkeydown="if(event.key==='Enter') sendMessage()">
            <button onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<script>
let chatConversationId = localStorage.getItem('chat_conversation_id');
let chatPollInterval = null;
const chatBody = document.getElementById('chatBody');
const chatMessages = document.getElementById('chatMessages');
const chatStartScreen = document.getElementById('chatStartScreen');
const chatInputArea = document.getElementById('chatInputArea');
const chatMessageInput = document.getElementById('chatMessageInput');

function toggleChat() {
    const popup = document.getElementById('chatPopup');
    popup.classList.toggle('open');

    if (popup.classList.contains('open') && chatConversationId) {
        loadMessages();
        chatPollInterval = setInterval(loadMessages, 5000);
    } else if (!popup.classList.contains('open')) {
        if (chatPollInterval) clearInterval(chatPollInterval);
    }
}

function startChat() {
    const name = document.getElementById('customerName').value.trim();
    if (!name) { alert('Please enter your name'); return; }

    const email = document.getElementById('customerEmail').value.trim();

    fetch('<?php echo e(route("livechat.start")); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: JSON.stringify({ customer_name: name, customer_email: email, message: 'Hi! I need some help.' })
    })
    .then(r => r.json())
    .then(data => {
        chatConversationId = data.conversation_id;
        localStorage.setItem('chat_conversation_id', chatConversationId);
        chatStartScreen.style.display = 'none';
        chatMessages.style.display = 'block';
        chatInputArea.style.display = 'flex';
        loadMessages();
        if (chatPollInterval) clearInterval(chatPollInterval);
        chatPollInterval = setInterval(loadMessages, 5000);
    });
}

function loadMessages() {
    if (!chatConversationId) return;

    fetch(`/livechat/${chatConversationId}/messages`)
    .then(r => r.json())
    .then(data => {
        chatMessages.innerHTML = '';
        data.messages.forEach(msg => {
            const div = document.createElement('div');
            div.className = `chat-msg ${msg.sender_type}`;
            let label = msg.sender_type === 'admin' ? (msg.is_bot ? '<i class="fa-solid fa-robot"></i> Bot' : 'Staff') : 'You';
            div.innerHTML = msg.message + '<small>' + label + ' &middot; ' + msg.time + '</small>';
            chatMessages.appendChild(div);
        });
        chatMessages.scrollTop = chatMessages.scrollHeight;
    });
}

function sendMessage() {
    const message = chatMessageInput.value.trim();
    if (!message || !chatConversationId) return;

    fetch(`/livechat/${chatConversationId}/send`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: JSON.stringify({ message: message })
    })
    .then(r => r.json())
    .then(() => {
        chatMessageInput.value = '';
        loadMessages();
    });
}

if (chatConversationId) {
    chatStartScreen.style.display = 'none';
    chatMessages.style.display = 'block';
    chatInputArea.style.display = 'flex';
}
</script>
<?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/frontend/partials/live-chat.blade.php ENDPATH**/ ?>