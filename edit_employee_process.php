<?php

    include "database.php";

    $id = $_POST['id'];

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $employee_id = $_POST['employee_id'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $position = $_POST['position'];
    $date_hired = $_POST['date_hired'];
    $salary = $_POST['salary'];
    $employment_status = $_POST['employment_status'];

    $check = "SELECT * FROM employees
            WHERE employee_id='$employee_id'
            AND id != '$id'";

    $result = mysqli_query($conn,$check);

    if(mysqli_num_rows($result) > 0){
        header("Location: employee.php?edit_id=$id&error=duplicate");
        exit();
    }

    $sql = "UPDATE employees SET
    firstname='$firstname',
    lastname='$lastname',
    employee_id='$employee_id',
    email='$email',
    phone='$phone',
    department='$department',
    position='$position',
    date_hired='$date_hired',
    salary='$salary',
    employment_status='$employment_status'
    WHERE id='$id'";

    if(mysqli_query($conn,$sql)){

        header("Location: employee.php");
        exit();

    }else{

        die("SQL Error: ".mysqli_error($conn));

    }
?>