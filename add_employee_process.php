<?php
    // Start the session (not actually used elsewhere in this script, but often kept
    // for consistency across pages, or in case flash messages get added later)
    session_start();

    // Load the database connection ($conn) from database.php
    include "database.php";

    // Pull each field from the submitted form ($_POST), trim whitespace,
    // and default to an empty string if the field wasn't sent at all
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

    // Build the INSERT statement for adding a new employee,
    // using the submitted values directly inside the query string
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
    // Before inserting, check if an employee with this same employee_id already exists
    $check = "SELECT * FROM employees WHERE employee_id='$employee_id'";
    $result = mysqli_query($conn, $check);

    // If the check query found one or more matching rows, it's a duplicate —
    // stop here and redirect back with an error flag in the URL
    if(mysqli_num_rows($result) > 0){
        header("Location: employee.php?error=duplicate");
        exit();
    }

    // Insert employee
    // No duplicate found, so proceed with inserting the new employee record
    if(mysqli_query($conn, $sql)){
        // Success — redirect back with a success flag in the URL
        header("Location: employee.php?success=added");
        exit();
    }else{
        // Insert failed — stop script execution and show the raw SQL error
        // (useful for debugging, but not something you'd want visible in production)
        die("SQL Error: " . mysqli_error($conn));
    }
?>