<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION['role'] ?? '') !== 'Department Head') {
    header("Location: dashboard.php");
    exit();
}

require_once 'database.php';

$employeeId = intval($_SESSION['employee_id'] ?? 0);

$departmentQuery = mysqli_prepare(
    $conn,
    "SELECT department
     FROM employees
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $departmentQuery,
    "i",
    $employeeId
);

mysqli_stmt_execute($departmentQuery);

$departmentResult = mysqli_stmt_get_result($departmentQuery);

$headData = mysqli_fetch_assoc($departmentResult);

$headDepartment = $headData['department'] ?? '';