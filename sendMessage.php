<?php
session_start();
include("connexion.php");

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user_id'];
$chat_user_id = $data['user_id'];
$message = $data['message'];

$user_id = mysqli_real_escape_string($cn, $user_id);
$chat_user_id = mysqli_real_escape_string($cn, $chat_user_id);
$message = mysqli_real_escape_string($cn, $message);

$sql = "INSERT INTO message (expediteur_id, destinataire_id, contenu, date_envoi) 
        VALUES ($user_id, $chat_user_id, '$message', NOW())";
$success = mysqli_query($cn, $sql);

echo json_encode(['status' => $success ? 'success' : 'error']);
?>