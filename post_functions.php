<?php
function savePostImage($postId, $imageFile) {
    $targetDir = "postImages/post_" . $postId . "/";
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($imageFile["name"], PATHINFO_EXTENSION));
    $targetFile = $targetDir . "post_image." . $imageFileType;
    
    // Check if image file is actual image
    $check = getimagesize($imageFile["tmp_name"]);
    if ($check === false) {
        return false;
    }
    
    // Check file size (5MB limit)
    if ($imageFile["size"] > 5000000) {
        return false;
    }
    
    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return false;
    }
    
    if (move_uploaded_file($imageFile["tmp_name"], $targetFile)) {
        // Update database with image filename
        include("connexion.php");
        $stmt = mysqli_prepare($cn, "UPDATE posts SET image = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", basename($targetFile), $postId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($cn);
        return true;
    }
    
    return false;
}