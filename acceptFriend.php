<?php
session_start();
include("connexion.php");

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; 
$friend_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($friend_id > 0 && $user_id !== $friend_id) {
    $user_id = mysqli_real_escape_string($cn, $user_id);
    $friend_id = mysqli_real_escape_string($cn, $friend_id);

    $update_sql = "UPDATE amie SET etat = 'accepted' WHERE id_utilisateur = $friend_id AND id_amie = $user_id";
    $success = mysqli_query($cn, $update_sql);

    if ($success) {
        header("Location: userInvitations.php");
    } else {
        header("Location: userInvitations.php");
    }
} else {
    header("Location: userInvitations.php");
}
?>
