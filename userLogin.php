<?php
if (isset($_POST['email']) && isset($_POST['userpassword'])) {
    include("connexion.php");
    $email = mysqli_real_escape_string($cn, $_POST['email']);
    $userpassword = mysqli_real_escape_string($cn, $_POST['userpassword']);
    $sql = "SELECT * FROM `users` WHERE email = '$email' AND password = '$userpassword'";
    $result = mysqli_query($cn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        header("Location: DashboardPage.php");
        mysqli_close($cn);
        exit;       
    } else {
        echo "Utilisateur non trouvé";
        mysqli_close($cn);
    }
}
?>
