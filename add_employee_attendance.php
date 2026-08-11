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
// CHECK TODAY'S ATTENDANCE
// ==========================

$checkQuery = mysqli_prepare(
    $conn,
    "SELECT id, time_in
     FROM attendance
     WHERE employee_id = ?
     AND attendance_date = CURDATE()"
);

mysqli_stmt_bind_param(
    $checkQuery,
    "i",
    $employeeId
);

mysqli_stmt_execute($checkQuery);

$result = mysqli_stmt_get_result($checkQuery);


// ==========================
// ALREADY TIMED IN?
// ==========================

if (mysqli_num_rows($result) > 0) {

    header(
        "Location: employee_dashboard.php?error=already_in"
    );

    exit();

}


// ==========================
// TIME IN
// ==========================

$insertQuery = mysqli_prepare(
    $conn,
    "INSERT INTO attendance
    (
        employee_id,
        attendance_date,
        time_in,
        status
    )
    VALUES
    (
        ?,
        CURDATE(),
        CURTIME(),
        'Present'
    )"
);

mysqli_stmt_bind_param(
    $insertQuery,
    "i",
    $employeeId
);


// ==========================
// SAVE
// ==========================

if (mysqli_stmt_execute($insertQuery)) {

    header(
        "Location: employee_dashboard.php?success=time_in"
    );

    exit();

} else {

    header(
        "Location: employee_dashboard.php?error=failed"
    );

    exit();

}

?>