<?php
    if(isset($_GET['id'])){
        include("connexion.php");
        $id=$_GET['id'];
        $sql="DELETE FROM `users` WHERE id='$id'";
        //Executer la requette
        $result=mysqli_query($cn,$sql);
        if($result){
            echo "L'utilisateur a été supprimé avec succès."; 
        }else{
            echo"Erreur :".mysqli_error();
            exit;
        }
        mysqli_close($cn);
        include("userListe.php");
    }
?>