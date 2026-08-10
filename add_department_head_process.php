<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'database.php';

$userRole = $_SESSION['role'] ?? '';


// ==========================
// ADMIN ONLY
// ==========================

if ($userRole !== 'Admin') {
    header("Location: dashboard.php");
    exit();
}


// ==========================
// GET FORM DATA
// ==========================

$departmentId = intval(
    $_POST['department_id'] ?? 0
);

$fullname = trim(
    $_POST['fullname'] ?? ''
);

$username = trim(
    $_POST['username'] ?? ''
);

$password = trim(
    $_POST['password'] ?? ''
);

$confirmPassword = trim(
    $_POST['confirmPassword'] ?? ''
);


// ==========================
// REQUIRED FIELDS
// ==========================

if (
    $departmentId <= 0 ||
    $fullname === '' ||
    $username === '' ||
    $password === '' ||
    $confirmPassword === ''
) {

    die("Please complete all fields.");

}


// ==========================
// PASSWORD MATCH
// ==========================

if ($password !== $confirmPassword) {

    die("Passwords do not match.");

}


// ==========================
// PASSWORD LENGTH
// ==========================

if (strlen($password) < 5) {

    die(
        "Password must be at least 5 characters long."
    );

}


// ==========================
// GET DEPARTMENT
// ==========================

$departmentQuery = mysqli_query(
    $conn,
    "SELECT
        id,
        department_name,
        department_head
     FROM departments
     WHERE id = $departmentId
     AND status = 'Active'"
);


if (
    !$departmentQuery ||
    mysqli_num_rows($departmentQuery) === 0
) {

    die("Department not found.");

}


$department = mysqli_fetch_assoc(
    $departmentQuery
);

$departmentName =
    $department['department_name'];


// ==========================
// CHECK DEPARTMENT HEAD NAME
// ==========================

if (
    empty($department['department_head'])
) {

    die(
        "This department does not have a department head assigned yet."
    );

}


// ==========================
// CHECK NAME
// ==========================

if (
    strcasecmp(
        trim($fullname),
        trim($department['department_head'])
    ) !== 0
) {

    die(
        "The name does not match the department head assigned to this department."
    );

}


// ==========================
// ESCAPE DATA
// ==========================

$fullnameEscaped =
    mysqli_real_escape_string(
        $conn,
        $fullname
    );

$usernameEscaped =
    mysqli_real_escape_string(
        $conn,
        $username
    );


// ==========================
// CHECK USERNAME
// ==========================

$usernameCheck = mysqli_query(
    $conn,
    "SELECT id
     FROM users
     WHERE username = '$usernameEscaped'"
);


if (
    $usernameCheck &&
    mysqli_num_rows($usernameCheck) > 0
) {

    die("Username already exists.");

}


// ==========================
// CHECK EXISTING DEPARTMENT HEAD ACCOUNT
// ==========================

$headAccountCheck = mysqli_query(
    $conn,
    "SELECT id
     FROM users
     WHERE fullname = '$fullnameEscaped'
     AND role = 'Department Head'"
);


if (
    $headAccountCheck &&
    mysqli_num_rows($headAccountCheck) > 0
) {

    die(
        "This department head already has an account."
    );

}


// ==========================
// HASH PASSWORD
// ==========================

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// ==========================
// CREATE ACCOUNT
// ==========================

$insertQuery = mysqli_query(
    $conn,
    "INSERT INTO users
    (
        employee_id,
        fullname,
        username,
        password,
        role
    )
    VALUES
    (
        NULL,
        '$fullnameEscaped',
        '$usernameEscaped',
        '$hashedPassword',
        'Department Head'
    )"
);


if ($insertQuery) {

    header("Location: add_department_head.php?success=added");

    exit();

}


die(
    "Failed to create account: "
    . mysqli_error($conn)
);

?>