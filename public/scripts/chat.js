(function() {
    const container = document.getElementById('chatMessages');
    if (!container) return;

    container.scrollTop = container.scrollHeight;

    const currentUserId = parseInt(container.dataset.userId);
    const userAvatar = container.dataset.userAvatar || '';
    const userInitial = container.dataset.userInitial || 'А';
    const avatarsUrl = container.dataset.avatarsUrl;
    const messagesUrl = container.dataset.messagesUrl;

    let lastMessageId = parseInt(container.dataset.lastMessageId || '0');

    function checkNewMessages() {
        fetch(messagesUrl + '?last_id=' + lastMessageId)
            .then(r => r.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        lastMessageId = msg.id;
                        addMessage(msg);
                    });
                }
            })
            .catch(() => {});
    }

    function addMessage(msg) {
        const div = document.createElement('div');
        const isMine = msg.sender_id == currentUserId;
        div.className = 'chat-message ' + (isMine ? 'mine' : 'theirs');
        div.dataset.id = msg.id;

        let avatarHtml = '';
        if (isMine) {
            avatarHtml = userAvatar
                ? `<img class="chat-avatar" src="${avatarsUrl}${userAvatar}" alt="avatar">`
                : `<div class="chat-avatar-placeholder">${userInitial}</div>`;
        } else {
            avatarHtml = msg.sender_avatar
                ? `<img class="chat-avatar" src="${avatarsUrl}${msg.sender_avatar}" alt="avatar">`
                : '<div class="chat-avatar-placeholder">А</div>';
        }

        div.innerHTML = avatarHtml +
            '<div class="chat-message-content">' +
                `<div class="chat-message-sender">${escapeHtml(msg.sender_name)}</div>` +
                `<div class="chat-message-text">${escapeHtml(msg.text)}</div>` +
                `<div class="chat-message-time">${msg.created_at}</div>` +
            '</div>';

        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function escapeHtml(text) {
        const el = document.createElement('div');
        el.textContent = text;
        return el.innerHTML;
    }

    setInterval(checkNewMessages, 3000);
})();
