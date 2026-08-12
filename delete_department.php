<?php

    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    require_once 'database.php';


    // ==========================
    // GET DEPARTMENT ID
    // ==========================

    $departmentId = $_GET['id'] ?? '';

    if ($departmentId === '' || !is_numeric($departmentId)) {
        header("Location: departments.php?error=invalid_id");
        exit();
    }

    $departmentId = (int) $departmentId;


    // ==========================
    // GET DEPARTMENT
    // ==========================

    $departmentQuery = mysqli_prepare($conn, "SELECT department_name
        FROM departments
        WHERE id = ?"
    );

    mysqli_stmt_bind_param($departmentQuery, "i", $departmentId);
    mysqli_stmt_execute($departmentQuery);
    $departmentResult = mysqli_stmt_get_result($departmentQuery);

    if (mysqli_num_rows($departmentResult) === 0) {
        header("Location: departments.php?error=not_found");
        exit();
    }

    $department = mysqli_fetch_assoc($departmentResult);
    $departmentName = $department['department_name'];

    // ==========================
    // CHECK EMPLOYEES
    // ==========================

    $employeeQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS total
        FROM employees
        WHERE department = ?"
    );

    mysqli_stmt_bind_param($employeeQuery, "s", $departmentName);
    mysqli_stmt_execute($employeeQuery);
    $employeeResult = mysqli_stmt_get_result($employeeQuery);
    $employeeCount = mysqli_fetch_assoc($employeeResult)['total'];


    // ==========================
    // PREVENT DELETE
    // ==========================

    if ($employeeCount > 0) {
        header("Location: departments.php?error=has_employees");
        exit();
    }

    // ==========================
    // DELETE DEPARTMENT
    // ==========================

    $deleteQuery = mysqli_prepare($conn, "DELETE FROM departments WHERE id = ?");
    mysqli_stmt_bind_param($deleteQuery, "i", $departmentId);

    if (mysqli_stmt_execute($deleteQuery)) {
        header("Location: departments.php?success=deleted");
        exit();
    } else {
        header("Location: departments.php?error=delete_failed");
        exit();
    }
    
?>