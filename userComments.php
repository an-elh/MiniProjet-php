<?php
session_start();
include("connexion.php");

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

// Handle POST request for adding new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['post_id']) && isset($data['content'])) {
        $stmt = mysqli_prepare($cn, "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iis", $data['post_id'], $_SESSION['user_id'], $data['content']);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to post comment']);
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle GET request for fetching comments
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['post_id'])) {
    $stmt = mysqli_prepare($cn, "
        SELECT c.*, u.nom, u.prenom, u.photo 
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = ?
        ORDER BY c.created_at DESC
    ");
    mysqli_stmt_bind_param($stmt, "i", $_GET['post_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }
    
    echo json_encode(['success' => true, 'comments' => $comments]);
    mysqli_stmt_close($stmt);
}

mysqli_close($cn);
?>