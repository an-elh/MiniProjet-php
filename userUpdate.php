<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

include("connexion.php");

if(isset($_GET["id"])){
    $id=$_GET['id'];

    // Set default values for optional fields
    $titre = isset($_POST['titre']) ? $_POST['titre'] : '';
    $nom = mysqli_real_escape_string($cn, $_POST['nom']);
    $prenom = mysqli_real_escape_string($cn, $_POST['prenom']);
    $email = mysqli_real_escape_string($cn, $_POST['email']);
    $dateNaissance = $_POST['Datedenaissance'];
    $branche = mysqli_real_escape_string($cn, $_POST['branche']);
    $fonction = mysqli_real_escape_string($cn, $_POST['fonction']);
    $address = mysqli_real_escape_string($cn, $_POST['address']);

    // Handle interests array
    $interets = isset($_POST['interet']) ? $_POST['interet'] : array();
    $interetsString = !empty($interets) ? implode(", ", $interets) : '';

    // Handle password update
    $passwordUpdate = "";
    if (!empty($_POST['password'])) {
        if ($_POST['password'] === $_POST['passwordConf']) {
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $passwordUpdate = ", `password`='$hashedPassword'";
        } else {
            $_SESSION['error'] = "Passwords do not match";
            header("Location: userProfil.php");
            exit;
        }
    }

    // Update user data
    $sql = "UPDATE users SET 
            `titre`=?, 
            `nom`=?, 
            `prenom`=?, 
            `email`=?, 
            `dateNaissance`=?, 
            `branche`=?, 
            `fonction`=?, 
            `adresse`=?, 
            `interets`=?
            $passwordUpdate 
            WHERE id=?";

    $stmt = mysqli_prepare($cn, $sql);

    if ($passwordUpdate) {
        mysqli_stmt_bind_param($stmt, "ssssssssss", 
            $titre, 
            $nom, 
            $prenom, 
            $email, 
            $dateNaissance, 
            $branche, 
            $fonction, 
            $address, 
            $interetsString,
            $_SESSION['user_id']
        );
    } else {
        mysqli_stmt_bind_param($stmt, "sssssssssi", 
            $titre, 
            $nom, 
            $prenom, 
            $email, 
            $dateNaissance, 
            $branche, 
            $fonction, 
            $address, 
            $interetsString,
            $_SESSION['user_id']
        );
    }

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Profile updated successfully";
    } else {
        $_SESSION['error'] = "Error updating profile: " . mysqli_error($cn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($cn);

    header("Location: userProfil.php");
    exit;
}
?>