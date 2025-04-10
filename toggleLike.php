<?php
session_start();
include("connexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['post_id'];
$user_id = $_SESSION['user_id'];

// Check if like exists
$check_sql = "SELECT id FROM likes WHERE post_id = ? AND user_id = ?";
$check_stmt = mysqli_prepare($cn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $post_id, $user_id);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    // Unlike
    $sql = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
    $liked = false;
} else {
    // Like
    $sql = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
    $liked = true;
}

$stmt = mysqli_prepare($cn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $post_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'liked' => $liked]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

mysqli_stmt_close($stmt);
mysqli_stmt_close($check_stmt);
mysqli_close($cn);