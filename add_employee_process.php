<?php
    session_start();
    include "database.php";

    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $employee_id = trim($_POST['employee_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $date_hired = trim($_POST['date_hired'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $employment_status = trim($_POST['employment_status'] ?? '');

    $sql = "INSERT INTO employees
    (
        firstname,
        lastname,
        employee_id,
        email,
        phone,
        department,
        position,
        date_hired,
        salary,
        employment_status
    )

    VALUES
    (
        '$firstname',
        '$lastname',
        '$employee_id',
        '$email',
        '$phone',
        '$department',
        '$position',
        '$date_hired',
        '$salary',
        '$employment_status'
    )";

    // Check duplicate employee ID
    $check = "SELECT * FROM employees WHERE employee_id='$employee_id'";
    $result = mysqli_query($conn, $check);

    if(mysqli_num_rows($result) > 0){

        header("Location: employee.php?error=duplicate");
        exit();

    }

    // Insert employee
    if(mysqli_query($conn, $sql)){

        header("Location: employee.php?success=added");
        exit();

    }else{

        die("SQL Error: " . mysqli_error($conn));

    }
?>