let chatPollInterval;
let lastMessageCount = 0;

function initChat(bookingId, currentUserId) {
    const chatHtml = `
        <div id="chatWindow" class="chat-window glass-card" style="display:none;">
            <div class="chat-header">
                <span>Chat with Driver/Rider</span>
                <button onclick="toggleChat()"><i class="fas fa-times"></i></button>
            </div>
            <div id="chatMessages" class="chat-messages"></div>
            <div class="chat-input-area">
                <input type="text" id="chatInput" placeholder="Type a message...">
                <button onclick="sendMessage(${bookingId}, ${currentUserId})"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
        <button id="chatToggleBtn" class="chat-toggle-btn" onclick="toggleChat()">
            <i class="fas fa-comment-dots"></i>
            <span id="chatBadge" class="chat-badge" style="display:none;">!</span>
        </button>
    `;
    document.body.insertAdjacentHTML('beforeend', chatHtml);

    // Start polling
    startChatPolling(bookingId, currentUserId);

    // Handle Enter key
    document.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && document.activeElement.id === 'chatInput') {
            sendMessage(bookingId, currentUserId);
        }
    });
}

function toggleChat() {
    const win = document.getElementById('chatWindow');
    const badge = document.getElementById('chatBadge');
    if (win.style.display === 'none') {
        win.style.display = 'flex';
        badge.style.display = 'none';
        scrollToBottom();
    } else {
        win.style.display = 'none';
    }
}

async function startChatPolling(bookingId, currentUserId) {
    chatPollInterval = setInterval(async () => {
        const response = await fetch(`/GetafeGRAB/rider/api/chat/get.php?booking_id=${bookingId}`);
        const result = await response.json();

        if (result.success) {
            if (result.messages.length > lastMessageCount) {
                renderMessages(result.messages, currentUserId);
                if (document.getElementById('chatWindow').style.display === 'none') {
                    document.getElementById('chatBadge').style.display = 'flex';
                }
                lastMessageCount = result.messages.length;
            }
        }
    }, 3000);
}

function renderMessages(messages, currentUserId) {
    const container = document.getElementById('chatMessages');
    container.innerHTML = '';
    messages.forEach(m => {
        const isMe = parseInt(m.sender_id) === parseInt(currentUserId);
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${isMe ? 'me' : 'them'}`;
        msgDiv.innerHTML = `
            <div class="msg-bubble">${m.message}</div>
            <div class="msg-time">${new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
        `;
        container.appendChild(msgDiv);
    });
    scrollToBottom();
}

async function sendMessage(bookingId, currentUserId) {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if (!message) return;

    input.value = '';
    const formData = new FormData();
    formData.append('booking_id', bookingId);
    formData.append('message', message);

    const response = await fetch('/GetafeGRAB/rider/api/chat/send.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    if (!result.success) {
        alert('Failed to send message');
    }
}

function scrollToBottom() {
    const container = document.getElementById('chatMessages');
    container.scrollTop = container.scrollHeight;
}
