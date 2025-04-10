<?php
session_start();
include("connexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user_id'];
    $friend_id = $input['user_id'];

    $user_id = mysqli_real_escape_string($cn, $user_id);
    $friend_id = mysqli_real_escape_string($cn, $friend_id);

    $sql = "DELETE FROM amie WHERE 
            (id_utilisateur = $user_id AND id_amie = $friend_id) OR 
            (id_utilisateur = $friend_id AND id_amie = $user_id)";
    $success = mysqli_query($cn, $sql);

    // Return a JSON response
    echo json_encode(['success' => $success]);
}
?>