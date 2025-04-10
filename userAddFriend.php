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

    $insert_sql = "INSERT INTO amie (id_utilisateur, id_amie, etat) VALUES (?, ?, 'pending')";
    $stmt = mysqli_prepare($cn, $insert_sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $friend_id);
    
    if (mysqli_stmt_execute($stmt)) {
        
        header("Location: userSearch.php");
    } else {
        
        header("Location: userSearch.php");
    }
} else {
    
    header("Location: userSearch.php");
}
?>