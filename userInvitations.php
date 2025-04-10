<?php
session_start();
include("connexion.php");

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_id = mysqli_real_escape_string($cn, $user_id);

$sql = "SELECT u.id, u.nom, u.prenom, u.photo, u.fonction, u.branche
        FROM users u
        INNER JOIN amie a ON u.id = a.id_utilisateur
        WHERE a.id_amie = $user_id AND a.etat = 'pending'";
$result = mysqli_query($cn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friend Requests | SocialApp</title>
    <link href="accueil.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h2>SocialApp</h2>
        <ul>
            <li><a href="DashboardPage.php"><i class="fas fa-home"></i> <span>Accueil</span></a></li>
            <li><a href="userProfil.php"><i class="fas fa-user"></i> <span>Profil</span></a></li>
            <li><a href="userSearch.php"><i class="fas fa-search"></i> <span>Recherche</span></a></li>
            <li class="active"><a href="userInvitations.php"><i class="fas fa-user-clock"></i> <span>Invitations</span></a></li>
            <li><a href="userListe.php"><i class="fas fa-users"></i> <span>Amis</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="requests-header">
            <h1><i class="fas fa-user-clock"></i> Friend Requests</h1>
            <p class="requests-subtitle">You have pending connection requests</p>
        </div>
        
        <div class="requests-grid">
            <?php if (mysqli_num_rows($result) > 0) : ?>
                <?php while ($data = mysqli_fetch_assoc($result)) : ?>
                    <div class="request-card">
                        <?php 
                            $img = ($data['photo'] == 'noImage.jpg') 
                                ? "userImage/noImage.jpg" 
                                : "userImage/user_userPhoto_" . $data['id'] . "/" . $data['photo'];
                        ?>
                        <img src="<?= $img ?>" alt="Profile Photo" class="request-avatar">
                        <div class="request-info">
                            <h3><?= $data['nom'] . ' ' . $data['prenom'] ?></h3>
                            <p class="user-title"><?= $data['fonction'] ?></p>
                            <p class="user-branch"><?= $data['branche'] ?></p>
                        </div>
                        <div class="request-actions">
                            <a href="acceptFriend.php?id=<?= $data['id'] ?>" class="accept-btn">
                                <i class="fas fa-check"></i> Accept
                            </a>
                            <a href="rejectFriend.php?id=<?= $data['id'] ?>" class="reject-btn">
                                <i class="fas fa-times"></i> Reject
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="no-requests">
                    <i class="fas fa-inbox"></i>
                    <h3>No pending requests</h3>
                    <p>You don't have any friend requests at this time</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>