<?php
    $host="localhost";
    $user="root";
    $pass="";
    $db="hrms_db_new";
    $conn=mysqli_connect($host,$user,$pass,$db);
    if(!$conn){
        die("Connection Failed");
    }
?>