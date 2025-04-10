<?php 
    $severName="localhost";
    $userName="root";
    $password="";
    $dataBaseName="BTS";
    $cn=mysqli_connect($severName,$userName,$password,$dataBaseName);
    if(mysqli_connect_errno()){
        die("Erreur de connexion".mysqli_connect_error());
    }
?>