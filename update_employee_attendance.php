<?php

    session_start();

    require_once 'database.php';


    // ==========================
    // LOGIN CHECK
    // ==========================

    if (!isset($_SESSION['user_id'])) {

        header("Location: login.php");
        exit();

    }


    // ==========================
    // EMPLOYEE CHECK
    // ==========================

    if (($_SESSION['role'] ?? '') !== 'Employee') {

        header("Location: dashboard.php");
        exit();

    }


    // ==========================
    // GET EMPLOYEE ID
    // ==========================

    $employeeId = intval(
        $_SESSION['employee_id'] ?? 0
    );

    if ($employeeId <= 0) {

        header(
            "Location: employee_dashboard.php?error=employee"
        );

        exit();

    }


    // ==========================
    // FIND TODAY'S ATTENDANCE
    // ==========================

    $checkQuery = mysqli_prepare(
        $conn,
        "SELECT id, time_in, time_out
        FROM attendance
        WHERE employee_id = ?
        AND attendance_date = CURDATE()
        LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $checkQuery,
        "i",
        $employeeId
    );

    mysqli_stmt_execute($checkQuery);

    $result = mysqli_stmt_get_result($checkQuery);


    // ==========================
    // CHECK RECORD
    // ==========================

    if (mysqli_num_rows($result) === 0) {

        header(
            "Location: employee_dashboard.php?error=no_time_in"
        );

        exit();

    }


    $attendance = mysqli_fetch_assoc($result);


    // ==========================
    // CHECK TIME IN
    // ==========================

    if (empty($attendance['time_in'])) {

        header(
            "Location: employee_dashboard.php?error=no_time_in"
        );

        exit();

    }


    // ==========================
    // CHECK ALREADY TIMED OUT
    // ==========================

    if (!empty($attendance['time_out'])) {

        header(
            "Location: employee_dashboard.php?error=already_out"
        );

        exit();

    }


    // ==========================
    // UPDATE TIME OUT
    // ==========================

    $updateQuery = mysqli_prepare(
        $conn,
        "UPDATE attendance
        SET time_out = CURTIME()
        WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $updateQuery,
        "i",
        $attendance['id']
    );


    // ==========================
    // SAVE
    // ==========================

    if (mysqli_stmt_execute($updateQuery)) {

        header(
            "Location: employee_dashboard.php?success=time_out"
        );

        exit();

    } else {

        header(
            "Location: employee_dashboard.php?error=failed"
        );

        exit();

    }

?>