<?php
if (isset($_GET["id"])) {
    $id = $_GET['id'];
    include("connexion.php");
    $sql = "SELECT * FROM `users` WHERE `id`='$id';";
    $result = mysqli_query($cn, $sql);
    $data = mysqli_fetch_assoc($result);
    $interetsArray = explode(", ", $data['interets']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="Dashboard-CSS.css" rel="stylesheet">
    <title>Edit Profile</title>
</head>
<body>
    <div class="sidebar">
        <h2>home</h2>
        <ul>
            <li><a href="userProfil.php">Profil</a></li>
            <li><a href="userSearch.php">Recherche</a></li>
            <li><a href="userInvitations.php">Invitation</a></li>
            <li><a href="userListe">Amis</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="navbar">
            <div class="search">
                <input type="text" placeholder="Rechercher un ami...">
                <button>Chercher</button>
            </div>
        </div>
        <div class="content">
            <div class="profile-card">
                <h2>Edit Profile</h2>
                <form method="post" action="userUpdate.php?id=<?php echo $id; ?>">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    
                    <div class="detail-item">
                        <div class="detail-label">Nom</div>
                        <input type="text" name="nom" id="nomId" required value="<?php echo $data['nom']; ?>">
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Prenom</div>
                        <input type="text" name="prenom" id="prenomId" required value="<?php echo $data['prenom']; ?>">
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <input type="email" name="email" id="emailId" required value="<?php echo $data['email']; ?>">
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Date de naissance</div>
                        <input type="date" name="Datedenaissance" id="DDNId" required value="<?php echo $data['dateNaissance']; ?>">
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Titre</div>
                        <label for="titreMId">M.<input type="radio" name="titre" id="titreMId" value="M" <?php if ($data['titre'] == 'M') echo 'checked'; ?>></label>
                        <label for="titreMmId">Mme.<input type="radio" name="titre" id="titreMmId" value="Mme" <?php if ($data['titre'] == 'Mme') echo 'checked'; ?>></label>
                        <label for="titreMllId">Mlle.<input type="radio" name="titre" id="titreMllId" value="Mlle" <?php if ($data['titre'] == 'Mlle') echo 'checked'; ?>></label>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Branche</div>
                        <select name="branche" id="brancheId">
                            <option value="DSI" <?php if ($data['branche'] == 'DSI') echo 'selected'; ?>>Developement des Systèmes d’Informations</option>
                            <option value="SE" <?php if ($data['branche'] == 'SE') echo 'selected'; ?>>Systèmes Electriques</option>
                            <option value="CPI" <?php if ($data['branche'] == 'CPI') echo 'selected'; ?>>Conception des produits Industriel</option>
                            <option value="PME" <?php if ($data['branche'] == 'PME') echo 'selected'; ?>>PME/PMI</option>
                        </select>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Fonction</div>
                        <select name="fonction" id="fctId">
                            <option value="Professeur" <?php if ($data['fonction'] == 'Professeur') echo 'selected'; ?>>Professeur</option>
                            <option value="Etudiant" <?php if ($data['fonction'] == 'Etudiant') echo 'selected'; ?>>Etudiant</option>
                            <option value="Administrateur" <?php if ($data['fonction'] == 'Administrateur') echo 'selected'; ?>>Administrateur</option>
                        </select>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Addresse</div>
                        <textarea name="address" id="addrId"><?php echo $data['adresse']; ?></textarea>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Interets</div>
                        <label for="LecId">Lecture<input type="checkbox" name="interet[]" id="LecId" value="LEC" <?php if (in_array('LEC', $interetsArray)) echo 'checked'; ?>></label>
                        <label for="voyId">Voyage<input type="checkbox" name="interet[]" id="voyId" value="VOY" <?php if (in_array('VOY', $interetsArray)) echo 'checked'; ?>></label>
                        <label for="ArtId">Arts<input type="checkbox" name="interet[]" id="ArtId" value="ART" <?php if (in_array('ART', $interetsArray)) echo 'checked'; ?>></label>
                        <label for="sportId">Sport<input type="checkbox" name="interet[]" id="sportId" value="SPORT" <?php if (in_array('SPORT', $interetsArray)) echo 'checked'; ?>></label>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Mot de passe</div>
                        <input type="password" name="password" id="pwId">
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Confirmation du mot de passe</div>
                        <input type="password" name="passwordConf" id="cpwId">
                    </div>

                    <div class="action-buttons">
                        <input type="submit" value="Envoyer" class="btn-edit">
                        <input type="reset" value="Effacer" class="btn-delete">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
} else {
    echo "Aucun enregistrement";
}
?>