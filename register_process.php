<?php
    session_start();
    include "database.php";
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');
    $employeeId = intval($_POST['employee_id'] ?? 0);


    // ==========================
    // REQUIRED FIELDS
    // ==========================

    if (
        $fullname === '' ||
        $username === '' ||
        $password === '' ||
        $confirmPassword === '' ||
        $employeeId <= 0
    ) {

        $_SESSION['register_error'] =
            'Please complete all fields.';

        header('Location: register.php');
        exit();
    }


    // ==========================
    // PASSWORD MATCH
    // ==========================

    if ($password !== $confirmPassword) {

        $_SESSION['register_error'] =
            'Passwords do not match.';

        header('Location: register.php');
        exit();
    }


    // ==========================
    // PASSWORD LENGTH
    // ==========================

    if (strlen($password) < 5) {

        $_SESSION['register_error'] =
            'Password must be at least 5 characters long.';

        header('Location: register.php');
        exit();
    }


    // ==========================
    // CHECK USERNAME
    // ==========================

    $checkSql = "
        SELECT id
        FROM users
        WHERE username = '$username'
    ";

    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {

        $_SESSION['register_error'] =
            'Username already exists.';

        header('Location: register.php');
        exit();
    }


    // ==========================
    // CHECK EMPLOYEE
    // ==========================

    $employeeCheckSql = "
        SELECT id
        FROM employees
        WHERE id = $employeeId
    ";

    $employeeCheckResult =
        mysqli_query($conn, $employeeCheckSql);

    if (mysqli_num_rows($employeeCheckResult) === 0) {

        $_SESSION['register_error'] =
            'Selected employee does not exist.';

        header('Location: register.php');
        exit();
    }


    // ==========================
    // CHECK IF EMPLOYEE
    // ALREADY HAS AN ACCOUNT
    // ==========================

    $accountCheckSql = "
        SELECT id
        FROM users
        WHERE employee_id = $employeeId
    ";

    $accountCheckResult =
        mysqli_query($conn, $accountCheckSql);

    if (mysqli_num_rows($accountCheckResult) > 0) {

        $_SESSION['register_error'] =
            'This employee already has an account.';

        header('Location: register.php');
        exit();
    }


    // ==========================
    // HASH PASSWORD
    // ==========================

    $hashedPassword =
        password_hash($password, PASSWORD_DEFAULT);


    // ==========================
    // CREATE ACCOUNT
    // ==========================

    $insertSql = "
        INSERT INTO users
        (
            employee_id,
            fullname,
            username,
            password,
            role
        )
        VALUES
        (
            $employeeId,
            '$fullname',
            '$username',
            '$hashedPassword',
            'Employee'
        )
    ";


    if (mysqli_query($conn, $insertSql)) {

        $_SESSION['register_success'] =
            'Account created successfully. Please sign in.';

        header('Location: login.php');
        exit();

    } else {

        $_SESSION['register_error'] =
            'Registration failed. Please try again.';

        header('Location: register.php');
        exit();
    }

?>

