<?php
    if(!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['Datedenaissance'])){
        //etablir la connexion a la base de donnees
        include("connexion.php");
        //Requette d'isnertion
        $nom = isset($_POST['nom']) ? mysqli_real_escape_string($cn, $_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? mysqli_real_escape_string($cn, $_POST['prenom']) : '';
        $titre = isset($_POST['titre']) ? mysqli_real_escape_string($cn, $_POST['titre']) : '';
        $email = isset($_POST['email']) ? mysqli_real_escape_string($cn, $_POST['email']) : '';
        $adresse = isset($_POST['address']) ? mysqli_real_escape_string($cn, $_POST['address']) : '';
        $fonction = isset($_POST['fonction']) ? mysqli_real_escape_string($cn, $_POST['fonction']) : '';
        $dateNaissance = mysqli_real_escape_string($cn, $_POST['Datedenaissance']);
        $branche = isset($_POST['branche']) ? mysqli_real_escape_string($cn, $_POST['branche']) : '';
        $password = isset($_POST['password']) ? mysqli_real_escape_string($cn,$_POST['password']) : '';
        $interetsArray = isset($_POST['interet']) ? $_POST['interet'] : [];
        $interet = !empty($interetsArray) ? join(", ", $interetsArray) : '';
        if(isset($_FILES['photo']) && $_FILES['photo']['error']==0){
            $photo = $_FILES['photo']['name'];
            $sql = "INSERT INTO `users`(`id`, `nom`, `prenom`, `titre`, `email`, `adresse`, `fonction`, `dateNaissance`, `branche`, `password`, `interets`, `photo`) 
                    VALUES (NULL, '$nom', '$prenom', '$titre', '$email', '$adresse', '$fonction', '$dateNaissance', '$branche', '$password', '$interet', '$photo')";
            mysqli_query($cn, $sql);
            $insertedId = mysqli_insert_id($cn);
            $userPhotoName = "userPhoto_".$insertedId;
            $userDirectory = "userImage/user_".$userPhotoName;
            mkdir($userDirectory);
            $source=$_FILES['photo']['tmp_name'];
            $destination = $userDirectory .'/'.$photo;
            move_uploaded_file($source, $destination);
            include("userListe.php");
        }
        else{
            $photo ="noImage.jpg";
            $sql = "INSERT INTO `users`(`id`, `nom`, `prenom`, `titre`, `email`, `adresse`, `fonction`, `dateNaissance`, `branche`, `password`, `interets`, `photo`) 
                    VALUES (NULL, '$nom', '$prenom', '$titre', '$email', '$adresse', '$fonction', '$dateNaissance', '$branche', '$password', '$interet', '$photo')";
            mysqli_query($cn, $sql);
            $insertedId = mysqli_insert_id($cn);
            $userPhotoName = "userPhoto_" . $insertedId;
            $userDirectory = "userImage/".$userPhotoName;
            mkdir($userDirectory);
            $source="userImage/noImage.jpg";
            $destination = $userDirectory . '/' .$photo;
            copy($source, $destination);
            header("Location:bts-pagedaccueil.html");
        }
    }else{
    echo"Veuillez saisir le nom,prenom et la date de naissance";
   }
?>