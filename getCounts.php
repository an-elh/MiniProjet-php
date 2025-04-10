<?php
session_start();
include("connexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    
    $sql = "SELECT 
        (SELECT COUNT(*) FROM likes WHERE post_id = ?) as like_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = ?) as comment_count";
        
    $stmt = mysqli_prepare($cn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $post_id, $post_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $counts = mysqli_fetch_assoc($result);
        echo json_encode([
            'success' => true, 
            'like_count' => $counts['like_count'],
            'comment_count' => $counts['comment_count']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Missing post_id']);
}

mysqli_close($cn);