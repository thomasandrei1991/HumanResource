<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'database.php';


// ==========================
// GET FORM DATA
// ==========================

$departmentName = trim($_POST['department_name'] ?? '');
$departmentHead = trim($_POST['department_head'] ?? '');
$status = $_POST['status'] ?? 'Active';


// ==========================
// VALIDATION
// ==========================

if ($departmentName === '') {
    header("Location: departments.php?error=empty");
    exit();
}


// ==========================
// CHECK DUPLICATE
// ==========================

$checkQuery = mysqli_prepare(
    $conn,
    "SELECT id FROM departments WHERE department_name = ?"
);

mysqli_stmt_bind_param(
    $checkQuery,
    "s",
    $departmentName
);

mysqli_stmt_execute($checkQuery);

$result = mysqli_stmt_get_result($checkQuery);

if (mysqli_num_rows($result) > 0) {

    header("Location: departments.php?error=duplicate");
    exit();
}


// ==========================
// INSERT DEPARTMENT
// ==========================

$insertQuery = mysqli_prepare(
    $conn,
    "INSERT INTO departments
    (department_name, department_head, status)
    VALUES (?, ?, ?)"
);

mysqli_stmt_bind_param(
    $insertQuery,
    "sss",
    $departmentName,
    $departmentHead,
    $status
);


if (mysqli_stmt_execute($insertQuery)) {

    header("Location: departments.php?success=added");
    exit();

} else {

    header("Location: departments.php?error=failed");
    exit();

}