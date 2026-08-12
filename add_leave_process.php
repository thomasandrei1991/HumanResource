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

    $employeeId = intval($_POST['employee_id'] ?? 0);
    $leaveType = trim($_POST['leave_type'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $totalDays = floatval($_POST['total_days'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');


    // ==========================
    // VALIDATION
    // ==========================

    if (
        $employeeId <= 0 ||
        empty($leaveType) ||
        empty($startDate) ||
        empty($endDate) ||
        $totalDays <= 0 ||
        empty($reason)
    ) {
        header("Location: leave_management.php?error=invalid");
        exit();
    }


    // ==========================
    // VALIDATE DATES
    // ==========================

    if ($endDate < $startDate) {
        header("Location: leave_management.php?error=date");
        exit();
    }

    // ==========================
    // INSERT LEAVE REQUEST
    // ==========================

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO leave_requests
        (
            employee_id,
            leave_type,
            start_date,
            end_date,
            total_days,
            reason,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, 'Pending')"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "isssds",
        $employeeId,
        $leaveType,
        $startDate,
        $endDate,
        $totalDays,
        $reason
    );


    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: leave_management.php?success=added");
        exit();
    }

    mysqli_stmt_close($stmt);
    header("Location: leave_management.php?error=failed");
    exit();

?>