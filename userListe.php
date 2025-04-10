<?php
session_start();
include("connexion.php");

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Update user's last activity time
$update_stmt = mysqli_prepare($cn, "UPDATE users SET last_activity = NOW() WHERE id = ?");
mysqli_stmt_bind_param($update_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($update_stmt);
mysqli_stmt_close($update_stmt);

// Get friends list with online status
$sql = "
    SELECT u.id, u.nom, u.prenom, u.photo, u.fonction, u.branche,
           CASE WHEN u.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE) 
                THEN 1 ELSE 0 END as is_online
    FROM users u
    INNER JOIN amie a ON (u.id = a.id_amie OR u.id = a.id_utilisateur)
    WHERE (a.id_utilisateur = ? OR a.id_amie = ?) AND a.etat = 'accepted' AND u.id != ?
    ORDER BY is_online DESC, u.nom ASC
";
$stmt = mysqli_prepare($cn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $user_id, $user_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$friends_data = [];
while ($data = mysqli_fetch_assoc($result)) {
    $friends_data[] = $data;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends | SocialApp</title>
    <link href="accueil.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</header>
<body>
    <div class="sidebar">
        <h2>SocialApp</h2>
        <ul>
            <li><a href="DashboardPage.php"><i class="fas fa-home"></i> <span>Accueil</span></a></li>
            <li><a href="userProfil.php"><i class="fas fa-user"></i> <span>Profil</span></a></li>
            <li><a href="userSearch.php"><i class="fas fa-search"></i> <span>Recherche</span></a></li>
            <li><a href="userInvitations.php"><i class="fas fa-user-clock"></i> <span>Invitations</span></a></li>
            <li class="active"><a href="userListe.php"><i class="fas fa-users"></i> <span>Amis</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="friends-header">
            <h1><i class="fas fa-users"></i> Your Friends</h1>
            <p class="friends-subtitle">Connect and chat with your friends</p>
        </div>
        
        <div class="friends-grid">
            <?php if (count($friends_data) > 0) : ?>
                <?php foreach ($friends_data as $friend) : ?>
                    <div class="friend-card" id="friend-card-<?= $friend['id'] ?>">
                        <div class="<?= $friend['is_online'] ? 'online-badge' : 'offline-badge' ?>"></div>
                        <?php 
                            $img = ($friend['photo'] == 'noImage.jpg') 
                                ? "userImage/noImage.jpg" 
                                : "userImage/user_userPhoto_" . $friend['id'] . "/" . $friend['photo'];
                        ?>
                        <img src="<?= $img ?>" alt="Profile Photo" class="friend-avatar">
                        <div class="friend-info">
                            <h3><?= $friend['nom'] . ' ' . $friend['prenom'] ?></h3>
                            <p class="friend-title"><?= $friend['fonction'] ?></p>
                            <p class="friend-branch"><?= $friend['branche'] ?></p>
                        </div>
                        <div class="friend-actions">
                            <button class="message-btn" onclick="openChat(<?= $friend['id'] ?>, '<?= htmlspecialchars($friend['nom'] . ' ' . $friend['prenom']) ?>')">
                                <i class="fas fa-comment"></i> Message
                            </button>
                            <button class="unfriend-btn" onclick="unfriend(<?= $friend['id'] ?>)">
                                <i class="fas fa-user-times"></i> Unfriend
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="no-friends">
                    <i class="fas fa-user-friends"></i>
                    <h3>No Friends Yet</h3>
                    <p>Start by searching for people to connect with</p>
                    <a href="userSearch.php" class="btn-primary" style="margin-top: 15px;">
                        <i class="fas fa-search"></i> Find Friends
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Friends Sidebar -->
    <div class="friends-sidebar" id="friends-sidebar">
        <div class="sidebar-header" onclick="toggleSidebar()">
            <div class="sidebar-title">Friends</div>
            <div class="online-count" id="online-count">0 Online</div>
        </div>
        <div class="sidebar-content" id="sidebar-content">
            <?php foreach ($friends_data as $friend) : ?>
                <div class="sidebar-friend" onclick="openChat(<?= $friend['id'] ?>, '<?= htmlspecialchars($friend['nom'] . ' ' . $friend['prenom']) ?>')">
                    <?php 
                        $img = ($friend['photo'] == 'noImage.jpg') 
                            ? "userImage/noImage.jpg" 
                            : "userImage/user_userPhoto_" . $friend['id'] . "/" . $friend['photo'];
                    ?>
                    <img src="<?= $img ?>" alt="Profile" class="sidebar-avatar">
                    <div class="sidebar-name"><?= $friend['nom'] . ' ' . $friend['prenom'] ?></div>
                    <div class="sidebar-status <?= $friend['is_online'] ? 'status-online' : 'status-offline' ?>"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Chat Container (will be populated dynamically) -->
    <div class="chat-container" id="chat-container"></div>

    <script>
        // Initialize with friends data from PHP
        const friendsData = <?php echo json_encode($friends_data); ?>;
        let activeChats = {};
        
        // DOM Ready
        document.addEventListener('DOMContentLoaded', function() {
            updateOnlineCount();
            
            // Initialize sidebar state
            const sidebarContent = document.getElementById('sidebar-content');
            sidebarContent.style.maxHeight = sidebarContent.scrollHeight + 'px';
        });
        
        // Toggle friends sidebar
        function toggleSidebar() {
            const content = document.getElementById('sidebar-content');
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        }
        
        // Update online friends count
        function updateOnlineCount() {
            const onlineCount = friendsData.filter(f => f.is_online == 1).length;
            document.getElementById('online-count').textContent = `${onlineCount} Online`;
        }
        
        // Open chat with a friend
        function openChat(userId, userName) {
            // Check if chat already exists
            if (activeChats[userId]) {
                // Bring to front
                const chatBox = document.getElementById(`chat-box-${userId}`);
                chatBox.style.display = 'flex';
                return;
            }
            
            // Create new chat box
            const chatContainer = document.getElementById('chat-container');
            const chatBox = document.createElement('div');
            chatBox.className = 'chat-box';
            chatBox.id = `chat-box-${userId}`;
            chatBox.innerHTML = `
                <div class="chat-header" onclick="toggleChat(${userId})">
                    <div class="chat-title">
                        <i class="fas fa-user"></i> ${userName}
                    </div>
                    <button class="chat-close" onclick="closeChat(${userId}, event)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="chat-content">
                    <div class="chat-messages" id="chat-messages-${userId}"></div>
                    <div class="chat-input-area">
                        <input type="text" class="chat-input" id="chat-input-${userId}" 
                               placeholder="Type a message..." onkeypress="if(event.keyCode==13) sendMessage(${userId})">
                        <button class="chat-send" onclick="sendMessage(${userId})">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            `;
            
            chatContainer.appendChild(chatBox);
            activeChats[userId] = true;
            
            // Load messages
            loadMessages(userId);
            
            // Start polling for new messages
            startMessagePolling(userId);
        }
        
        // Close chat
        function closeChat(userId, event) {
            if (event) event.stopPropagation();
            const chatBox = document.getElementById(`chat-box-${userId}`);
            if (chatBox) {
                chatBox.remove();
                delete activeChats[userId];
                clearInterval(chatBox.pollInterval);
            }
        }
        
        // Toggle chat visibility
        function toggleChat(userId) {
            const chatBox = document.getElementById(`chat-box-${userId}`);
            const content = chatBox.querySelector('.chat-content');
            if (content.style.display === 'none') {
                content.style.display = 'flex';
            } else {
                content.style.display = 'none';
            }
        }
        
        // Load messages for a chat
        function loadMessages(userId) {
            fetch(`loadMessages.php?user_id=${userId}`)
                .then(response => response.json())
                .then(messages => {
                    const messagesContainer = document.getElementById(`chat-messages-${userId}`);
                    messagesContainer.innerHTML = '';
                    
                    messages.forEach(msg => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = msg.sender === 'me' ? 'message message-out' : 'message message-in';
                        messageDiv.textContent = msg.text;
                        messagesContainer.appendChild(messageDiv);
                    });
                    
                    // Scroll to bottom
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                });
        }
        
        // Send message
        function sendMessage(userId) {
            const input = document.getElementById(`chat-input-${userId}`);
            const message = input.value.trim();
            
            if (message) {
                fetch('sendMessage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        message: message
                    })
                })
                .then(() => {
                    input.value = '';
                    loadMessages(userId);
                });
            }
        }
        
        // Start polling for new messages
        function startMessagePolling(userId) {
            const chatBox = document.getElementById(`chat-box-${userId}`);
            chatBox.pollInterval = setInterval(() => {
                if (activeChats[userId]) {
                    loadMessages(userId);
                } else {
                    clearInterval(chatBox.pollInterval);
                }
            }, 3000);
        }
        
        // Unfriend a user
        function unfriend(userId) {
            if (confirm(`Are you sure you want to remove this friend?`)) {
                fetch('unfriend.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: userId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove friend card
                        const card = document.getElementById(`friend-card-${userId}`);
                        if (card) card.remove();
                        
                        // Remove from sidebar
                        const sidebarItems = document.querySelectorAll('.sidebar-friend');
                        sidebarItems.forEach(item => {
                            if (item.onclick.toString().includes(userId)) {
                                item.remove();
                            }
                        });
                        
                        // Close chat if open
                        if (activeChats[userId]) {
                            closeChat(userId);
                        }
                        
                        // Update friends data
                        friendsData = friendsData.filter(f => f.id != userId);
                        updateOnlineCount();
                    } else {
                        alert('Failed to remove friend: ' + (data.message || 'Unknown error'));
                    }
                });
            }
        }
    </script>
</body>
</html>