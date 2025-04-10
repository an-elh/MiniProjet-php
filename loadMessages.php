<?php
session_start();
include("connexion.php");

$user_id = $_SESSION['user_id'];
$chat_user_id = $_GET['user_id'];

// Sanitize input to prevent SQL injection
$user_id = mysqli_real_escape_string($cn, $user_id);
$chat_user_id = mysqli_real_escape_string($cn, $chat_user_id);

// Fetch messages using a normal query
$sql = "
    SELECT expediteur_id, destinataire_id, contenu, 
           CASE WHEN expediteur_id = $user_id THEN 'me' ELSE 'them' END AS sender
    FROM message
    WHERE (expediteur_id = $user_id AND destinataire_id = $chat_user_id) 
       OR (expediteur_id = $chat_user_id AND destinataire_id = $user_id)
    ORDER BY date_envoi ASC
";
$result = mysqli_query($cn, $sql);

$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = ['text' => $row['contenu'], 'sender' => $row['sender']];
}

echo json_encode($messages);
?>