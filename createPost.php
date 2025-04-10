<?php
session_start();
include("connexion.php");
include("post_functions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    
    // Insert post
    $stmt = mysqli_prepare($cn, "INSERT INTO posts (user_id, content) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "is", $user_id, $content);
    mysqli_stmt_execute($stmt);
    $post_id = mysqli_insert_id($cn);
    mysqli_stmt_close($stmt);
    
    // Handle image upload if present
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        savePostImage($post_id, $_FILES["image"]);
    }
    
    header("Location: DashboardPage.php");
    exit;
}