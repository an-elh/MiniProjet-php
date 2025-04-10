<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

include("connexion.php");

// Fetch posts with user info, like counts, and comment counts
$sql = "SELECT p.*, u.nom, u.prenom, u.photo,
       (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
       (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count,
       EXISTS(SELECT 1 FROM likes WHERE post_id = p.id AND user_id = ?) as user_liked
       FROM posts p 
       JOIN users u ON p.user_id = u.id 
       WHERE p.user_id = ?  -- User's own posts
       OR p.user_id IN (    -- Friends' posts
           SELECT id_amie 
           FROM amie 
           WHERE id_utilisateur = ? 
           AND etat = 'accepted'
           UNION
           SELECT id_utilisateur
           FROM amie 
           WHERE id_amie = ?
           AND etat = 'accepted'
       )
       ORDER BY p.created_at DESC";

$stmt = mysqli_prepare($cn, $sql);
mysqli_stmt_bind_param($stmt, "iiii", 
    $_SESSION['user_id'],  // For user_liked subquery
    $_SESSION['user_id'],  // For own posts
    $_SESSION['user_id'],  // For friends where user is id_utilisateur
    $_SESSION['user_id']   // For friends where user is id_amie
);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}

mysqli_stmt_close($stmt);
mysqli_close($cn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Network</title>
    <link href="accueil.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h2>SocialApp</h2>
        <ul>
            <li class="active"><a href="DashboardPage.php"><i class="fas fa-home"></i> <span>Accueil</span></a></li>
            <li><a href="userProfil.php"><i class="fas fa-user"></i> <span>Profil</span></a></li>
            <li><a href="userSearch.php"><i class="fas fa-search"></i> <span>Recherche</span></a></li>
            <li><a href="userInvitations.php"><i class="fas fa-user-plus"></i> <span>Invitation</span></a></li>
            <li><a href="userListe.php"><i class="fas fa-users"></i> <span>Amis</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="create-post">
            <form action="createPost.php" method="post" enctype="multipart/form-data">
                <textarea name="content" class="post-input" placeholder="What's on your mind?"></textarea>
                <div class="post-actions">
                    <label for="image-upload" class="file-upload">
                        <i class="fas fa-image"></i> Photo
                    </label>
                    <input id="image-upload" type="file" name="image" accept="image/*" style="display: none;">
                    <button type="submit" class="post-btn">Post</button>
                </div>
            </form>
        </div>

        <?php foreach ($posts as $post): ?>
            <div class="post-card" data-post-id="<?php echo $post['id']; ?>">
                <div class="post-header">
                    <?php 
                        $profileImg = ($post['photo'] == 'noImage.jpg') 
                            ? "userImage/noImage.jpg" 
                            : "userImage/user_userPhoto_" . $post['user_id'] . "/" . $post['photo'];
                    ?>
                    <img src="<?php echo $profileImg; ?>" alt="Profile" class="post-avatar">
                    <div class="post-user-info">
                        <h4><?php echo htmlspecialchars($post['nom'] . ' ' . $post['prenom']); ?></h4>
                        <span class="post-time"><?php echo date('F j, Y g:i a', strtotime($post['created_at'])); ?></span>
                    </div>
                </div>
                
                <div class="post-content">
                    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    <?php if ($post['image']): ?>
                        <img src="postImages/post_<?php echo $post['id']; ?>/<?php echo $post['image']; ?>" 
                             alt="Post image" class="post-image">
                    <?php endif; ?>
                </div>

                <div class="post-footer">
                    <div class="post-stats">
                        <div class="stat-item <?php echo $post['user_liked'] ? 'liked' : ''; ?>"
                             onclick="toggleLike(<?php echo $post['id']; ?>, event)">
                            <i class="fas fa-heart"></i>
                            <span><?php echo $post['like_count']; ?></span>
                        </div>
                        <div class="stat-item" onclick="showComments(<?php echo $post['id']; ?>)">
                            <i class="fas fa-comment"></i>
                            <span><?php echo $post['comment_count']; ?></span>
                        </div>
                    </div>
                    
                    <!-- Comments section (initially hidden) -->
                    <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display: none;">
                        <div class="comment-form">
                            <input type="text" placeholder="Write a comment..." class="comment-input">
                            <button class="comment-btn">Post</button>
                        </div>
                        <div class="comments-list">
                            <!-- Comments would be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function updateCounts(postId) {
            fetch(`getCounts.php?post_id=${postId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const postCard = document.querySelector(`[data-post-id="${postId}"]`);
                        const likeCount = postCard.querySelector('.fa-heart + span');
                        const commentCount = postCard.querySelector('.fa-comment + span');
                        
                        likeCount.textContent = data.like_count;
                        commentCount.textContent = data.comment_count;
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function toggleLike(postId, event) {  // Add event parameter
            const likeButton = event.currentTarget;
            const likeCount = likeButton.querySelector('span');
            const isLiked = likeButton.classList.contains('liked');

            // Update UI immediately
            if (!isLiked) {
                likeButton.classList.add('liked');
                likeCount.textContent = parseInt(likeCount.textContent) + 1;
            } else {
                likeButton.classList.remove('liked');
                likeCount.textContent = parseInt(likeCount.textContent) - 1;
            }

            // Send request to server
            fetch('toggleLike.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ post_id: postId })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert changes if server request failed
                    if (!isLiked) {
                        likeButton.classList.remove('liked');
                        likeCount.textContent = parseInt(likeCount.textContent) - 1;
                    } else {
                        likeButton.classList.add('liked');
                        likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert changes on error
                if (!isLiked) {
                    likeButton.classList.remove('liked');
                    likeCount.textContent = parseInt(likeCount.textContent) - 1;
                } else {
                    likeButton.classList.add('liked');
                    likeCount.textContent = parseInt(likeCount.textContent) + 1;
                }
            });
        }

        function showComments(postId) {
            const commentsSection = document.getElementById(`comments-${postId}`);
            const commentsList = commentsSection.querySelector('.comments-list');
            const commentInput = commentsSection.querySelector('.comment-input');
            const commentButton = commentsSection.querySelector('.comment-btn');

            // Function to handle comment posting
            const postComment = () => {
                const content = commentInput.value.trim();
                if (content) {
                    fetch('userComments.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            post_id: postId,
                            content: content
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear input
                            commentInput.value = '';
                            
                            // Update comment count immediately
                            const commentCountSpan = commentsSection.parentElement.querySelector('.fa-comment + span');
                            commentCountSpan.textContent = parseInt(commentCountSpan.textContent) + 1;
                            
                            // Reload comments
                            showComments(postId);
                        }
                    });
                }
            };

            if (commentsSection.style.display === 'none') {
                commentsSection.style.display = 'block';
                
                // Load existing comments
                fetch(`userComments.php?post_id=${postId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            commentsList.innerHTML = data.comments.map(comment => `
                                <div class="comment">
                                    <img src="${comment.photo === 'noImage.jpg' 
                                        ? 'userImage/noImage.jpg' 
                                        : 'userImage/user_userPhoto_' + comment.user_id + '/' + comment.photo}" 
                                        alt="Profile" class="comment-avatar">
                                    <div class="comment-content">
                                        <div class="comment-header">
                                            <strong>${comment.nom} ${comment.prenom}</strong>
                                            <span class="comment-time">${new Date(comment.created_at).toLocaleString()}</span>
                                        </div>
                                        <p>${comment.content}</p>
                                    </div>
                                </div>
                            `).join('');
                        }
                    });

                // Add event listeners if not already added
                if (!commentButton.hasListener) {
                    commentButton.hasListener = true;
                    
                    // Add click event listener
                    commentButton.addEventListener('click', postComment);
                    
                    // Add keypress event listener for Enter key
                    commentInput.addEventListener('keypress', (event) => {
                        if (event.key === 'Enter') {
                            event.preventDefault(); // Prevent default form submission
                            postComment();
                        }
                    });
                }
            } else {
                commentsSection.style.display = 'none';
            }
        }
        
    </script>
</body>
</html>