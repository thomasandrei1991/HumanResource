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

    $departmentId = intval($_POST['id'] ?? 0);
    $departmentName = trim($_POST['department_name'] ?? '');
    $departmentHead = trim($_POST['department_head'] ?? '');
    $status = $_POST['status'] ?? 'Active';


    // ==========================
    // VALIDATION
    // ==========================

    if ($departmentId <= 0 || $departmentName === '') {
        header("Location: departments.php?error=invalid");
        exit();
    }


    // ==========================
    // CHECK DUPLICATE
    // ==========================

    $checkQuery = mysqli_prepare($conn, "SELECT id
        FROM departments
        WHERE department_name = ?
        AND id != ?"
    );

    mysqli_stmt_bind_param($checkQuery, "si", $departmentName, $departmentId);
    mysqli_stmt_execute($checkQuery);
    $result = mysqli_stmt_get_result($checkQuery);

    if (mysqli_num_rows($result) > 0) {
        header("Location: departments.php?error=duplicate");
        exit();
    }


    // ==========================
    // UPDATE DEPARTMENT
    // ==========================

    $updateQuery = mysqli_prepare($conn, "UPDATE departments SET department_name = ?, department_head = ?, 
        status = ? WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $updateQuery,
        "sssi",
        $departmentName,
        $departmentHead,
        $status,
        $departmentId
    );


    // ==========================
    // EXECUTE
    // ==========================

    if (mysqli_stmt_execute($updateQuery)) {
        header("Location: departments.php?success=updated");
        exit();

    } else {
        header("Location: departments.php?error=update_failed");
        exit();

    }

    
?>