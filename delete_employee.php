<?php

    include "database.php";

    $id = $_GET['id'];

    $sql = "DELETE FROM employees WHERE id='$id'";

    if(mysqli_query($conn, $sql)){

        header("Location: employee.php?success=deleted");
        exit();

    }else{

        die("SQL Error: " . mysqli_error($conn));

    }

?>


