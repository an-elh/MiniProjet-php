<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
include("connexion.php");
$sql = "SELECT * FROM `users` WHERE `id`='$user_id';";
$result = mysqli_query($cn, $sql);
$data = mysqli_fetch_assoc($result);
$interetsArray = explode(", ", $data['interets']);  
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link href="accueil.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h2>SocialApp</h2>
        <ul>
            <li><a href="DashboardPage.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li class="active"><a href="userProfil.php"><i class="fas fa-user"></i> <span>Profil</span></a></li>
            <li><a href="userSearch.php"><i class="fas fa-search"></i> <span>Recherche</span></a></li>
            <li><a href="userInvitations.php"><i class="fas fa-user-plus"></i> <span>Invitations</span></a></li>
            <li><a href="userListe.php"><i class="fas fa-users"></i> <span>Amis</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="profile-container">
            <div class="profile-card">
                <div class="profile-header">
                    <?php 
                        $profileImg = ($data['photo'] == 'noImage.jpg') 
                            ? "userImage/noImage.jpg" 
                            : "userImage/user_userPhoto_" . $user_id . "/" . $data['photo'];
                    ?>
                    <img src="<?php echo $profileImg; ?>" class="profile-avatar" alt="Photo de Profil">
                    <div class="profile-info">
                        <h2><?php echo $data['titre'] . ' ' . $data['prenom'] . ' ' . $data['nom']; ?></h2>
                        <div class="profile-badges">
                            <span class="profile-badge"><?php echo $data['fonction']; ?></span>
                            <span class="profile-badge"><?php echo $data['branche']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="profile-details">
                    <div class="detail-section">
                        <h3 class="section-title"><i class="fas fa-info-circle"></i> Informations de Base</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-envelope"></i> Email</div>
                                <div class="detail-value"><?php echo $data['email']; ?></div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-map-marker-alt"></i> Adresse</div>
                                <div class="detail-value"><?php echo $data['adresse']; ?></div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-birthday-cake"></i> Date de Naissance</div>
                                <div class="detail-value"><?php 
                                    setlocale(LC_TIME, 'fr_FR.utf8');
                                    echo strftime("%d %B %Y", strtotime($data['dateNaissance'])); 
                                ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h3 class="section-title"><i class="fas fa-heart"></i> Centres d'Intérêt</h3>
                        <div class="interests-container">
                            <?php if (isset($data['interets']) && !empty($data['interets'])): ?>
                                <?php foreach ($interetsArray as $interest): ?>
                                    <span class="interest-tag"><?php 
                                        $interestLabels = [
                                            'LEC' => 'Lecture',
                                            'VOY' => 'Voyage',
                                            'ART' => 'Art',
                                            'SPORT' => 'Sport'
                                        ];
                                        echo isset($interestLabels[$interest]) ? $interestLabels[$interest] : $interest;
                                    ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-interests">Aucun centre d'intérêt spécifié</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="profile-actions">
                    <button id="openModalBtn" class="btn-edit"><i class="fas fa-edit"></i> Modifier le Profil</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-edit"></i> Modifier Profile</h3>
                <button class="close-btn" id="closeModalBtn">&times;</button>
            </div>
            
            <form method="post" action="userUpdate.php?id=<?php echo $user_id; ?>" class="modal-form">
                <input type="hidden" name="id" value="<?php echo $user_id; ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nomId">Nom</label>
                        <input type="text" name="nom" id="nomId" required value="<?php echo $data['nom']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="prenomId">Prenom</label>
                        <input type="text" name="prenom" id="prenomId" required value="<?php echo $data['prenom']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="emailId">Email</label>
                        <input type="email" name="email" id="emailId" required value="<?php echo $data['email']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="DDNId">Date de naissance</label>
                        <input type="date" name="Datedenaissance" id="DDNId" required value="<?php echo $data['dateNaissance']; ?>">
                    </div>

                    <div class="form-group">
                        <label>Titre</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="titre" value="M" <?php if ($data['titre'] == 'M') echo 'checked'; ?>>
                                <span>M.</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="titre" value="Mme" <?php if ($data['titre'] == 'Mme') echo 'checked'; ?>>
                                <span>Mme.</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="titre" value="Mlle" <?php if ($data['titre'] == 'Mlle') echo 'checked'; ?>>
                                <span>Mlle</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="brancheId">Branche</label>
                        <select name="branche" id="brancheId">
                            <option value="DSI" <?php if ($data['branche'] == 'DSI') echo 'selected'; ?>>Developement des Systèmes d'Informations</option>
                            <option value="SE" <?php if ($data['branche'] == 'SE') echo 'selected'; ?>>Systèmes Electriques</option>
                            <option value="CPI" <?php if ($data['branche'] == 'CPI') echo 'selected'; ?>>Conception des produits Industriel</option>
                            <option value="PME" <?php if ($data['branche'] == 'PME') echo 'selected'; ?>>PME/PMI</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fctId">Fonction</label>
                        <select name="fonction" id="fctId">
                            <option value="Professeur" <?php if ($data['fonction'] == 'Professeur') echo 'selected'; ?>>Professeur</option>
                            <option value="Etudiant" <?php if ($data['fonction'] == 'Etudiant') echo 'selected'; ?>>Etudiant</option>
                            <option value="Administrateur" <?php if ($data['fonction'] == 'Administrateur') echo 'selected'; ?>>Administrateur</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="addrId">Adresse</label>
                        <textarea name="address" id="addrId" rows="3"><?php echo $data['adresse']; ?></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label>Interests</label>
                        <div class="checkbox-group">
                            <label class="checkbox-option">
                                <input type="checkbox" name="interet[]" value="LEC" <?php if (in_array('LEC', $interetsArray)) echo 'checked'; ?>>
                                <span>Lecture</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="interet[]" value="VOY" <?php if (in_array('VOY', $interetsArray)) echo 'checked'; ?>>
                                <span>Voyage</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="interet[]" value="ART" <?php if (in_array('ART', $interetsArray)) echo 'checked'; ?>>
                                <span>Arts</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="interet[]" value="SPORT" <?php if (in_array('SPORT', $interetsArray)) echo 'checked'; ?>>
                                <span>Sport</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pwId">Mot de passe</label>
                        <input type="password" name="password" id="pwId" placeholder="Leave blank to keep current">
                    </div>

                    <div class="form-group">
                        <label for="cpwId">Confirmation de mot de passe</label>
                        <input type="password" name="passwordConf" id="cpwId" placeholder="Leave blank to keep current">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                    <button type="button" class="btn-secondary" id="closeModalBtn"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("editModal");
        const openModalBtn = document.getElementById("openModalBtn");
        const closeModalBtn = document.getElementById("closeModalBtn");

        openModalBtn.addEventListener("click", () => {
            modal.style.display = "flex";
            document.body.style.overflow = "hidden";
        });

        closeModalBtn.addEventListener("click", () => {
            modal.style.display = "none";
            document.body.style.overflow = "auto";
        });

        window.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
                document.body.style.overflow = "auto";
            }
        });
    </script>
</body>
</html>