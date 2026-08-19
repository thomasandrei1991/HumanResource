<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    exit(json_encode([]));
}

$userRole = $_SESSION['role'] ?? '';
$isAdminOrHR = ($userRole === 'Admin' || $userRole === 'HR');
$isDepartmentHead = ($userRole === 'Department Head');

if (!$isAdminOrHR && !$isDepartmentHead) {
    exit(json_encode([]));
}

// Get Department Head's department name
$headDepartment = '';
if ($isDepartmentHead) {
    $headName = $_SESSION['fullname'] ?? '';
    $departmentQuery = mysqli_prepare($conn, "SELECT department_name FROM departments WHERE department_head = ? AND status = 'active' LIMIT 1");
    mysqli_stmt_bind_param($departmentQuery, "s", $headName);
    mysqli_stmt_execute($departmentQuery);
    $departmentResult = mysqli_stmt_get_result($departmentQuery);
    if ($departmentData = mysqli_fetch_assoc($departmentResult)) {
        $headDepartment = $departmentData['department_name'] ?? '';
    }
}

// Search parameter
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchParam = $searchTerm . '%';

if ($isAdminOrHR) {
    if (!empty($searchTerm)) {
        $query = "SELECT id, employee_id, firstname, lastname, email, phone, department, position, date_hired, salary, employment_status 
                  FROM employees 
                  WHERE firstname LIKE ? OR lastname LIKE ? OR department LIKE ? OR position LIKE ? OR employee_id LIKE ? 
                  ORDER BY lastname ASC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
    } else {
        $query = "SELECT id, employee_id, firstname, lastname, email, phone, department, position, date_hired, salary, employment_status 
                  FROM employees 
                  ORDER BY lastname ASC";
        $stmt = mysqli_prepare($conn, $query);
    }
} else {
    if (!empty($searchTerm)) {
        $query = "SELECT id, employee_id, firstname, lastname, email, phone, department, position, date_hired, salary, employment_status 
                  FROM employees 
                  WHERE LOWER(department) = LOWER(?) 
                  AND (firstname LIKE ? OR lastname LIKE ? OR position LIKE ? OR employee_id LIKE ?) 
                  ORDER BY lastname ASC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $headDepartment, $searchParam, $searchParam, $searchParam, $searchParam);
    } else {
        $query = "SELECT id, employee_id, firstname, lastname, email, phone, department, position, date_hired, salary, employment_status 
                  FROM employees 
                  WHERE LOWER(department) = LOWER(?) 
                  ORDER BY lastname ASC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $headDepartment);
    }
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$employees = [];
while ($row = mysqli_fetch_assoc($result)) {
    $employees[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['employees' => $employees, 'isAdminOrHR' => $isAdminOrHR]);