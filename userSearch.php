<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
include("connexion.php");

$search = isset($_GET['search']) ? mysqli_real_escape_string($cn, $_GET['search']) : '';

$sql = "SELECT * FROM `users` 
        WHERE `id` != '$user_id' 
        AND `id` NOT IN (SELECT `id_amie` FROM `amie` WHERE `id_utilisateur` = '$user_id')";

if (!empty($search)) {
    $sql .= " AND (`nom` LIKE '%$search%' OR `prenom` LIKE '%$search%')";
}

$result = mysqli_query($cn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Search | SocialApp</title>
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
            <li class="active"><a href="userSearch.php"><i class="fas fa-search"></i> <span>Recherche</span></a></li>
            <li><a href="userInvitations.php"><i class="fas fa-user-plus"></i> <span>Invitation</span></a></li>
            <li><a href="userListe.php"><i class="fas fa-users"></i> <span>Amis</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="search-header">
            <h1><i class="fas fa-search"></i> Find Friends</h1>
            <form method="GET" action="userSearch.php" class="search-form">
                <div class="search-input-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="Search by name..." 
                           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button type="submit" class="search-btn">Search</button>
                </div>
            </form>
        </div>
        
        <div class="users-grid">
            <?php if (mysqli_num_rows($result) > 0) : ?>
                <?php while ($data = mysqli_fetch_assoc($result)) : ?>
                    <div class="user-card">
                        <?php 
                            $img = ($data['photo'] == 'noImage.jpg') 
                                ? "userImage/noImage.jpg" 
                                : "userImage/user_userPhoto_" . $data['id'] . "/" . $data['photo'];
                        ?>
                        <img src="<?= $img ?>" alt="Profile Photo" class="user-avatar">
                        <div class="user-info">
                            <h3><?= $data['nom'] . ' ' . $data['prenom'] ?></h3>
                            <p class="user-title"><?= $data['fonction'] ?></p>
                            <p class="user-branch"><?= $data['branche'] ?></p>
                        </div>
                        <a href="userAddFriend.php?id=<?= $data['id'] ?>" class="add-friend-btn">
                            <i class="fas fa-user-plus"></i> Add Friend
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="no-results">
                    <i class="fas fa-user-slash"></i>
                    <p>No users found matching your search</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>