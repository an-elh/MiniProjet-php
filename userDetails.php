<?php 
    if(isset($_GET['id'])){
        $id=$_GET['id'];
        include("connexion.php");
        $sql = "SELECT * FROM `users` WHERE `id`='$id';";
        //Executer la requette
        $result=mysqli_query($cn,$sql);
        
        if(mysqli_num_rows($result)==1){
            $data=mysqli_fetch_assoc($result);
            echo "<table border=1>";
        foreach ($data as $key => $value) {
            echo "<tr><td>" . $key . "</td><td>" .$value . "</td></tr>";
        }
        echo "</table>";
        }
        else{
            echo"aucune resultat";
        }
    }else{
        echo"Pas d'identifiant correspondant";
    }
?>