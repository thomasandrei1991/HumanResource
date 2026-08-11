<?php

session_start();
require_once 'database.php';

// ==========================================================
// LOGIN CHECK
// ==========================================================

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// ==========================================================
// CURRENT USER & ACCESS CHECK
// ==========================================================

$userRole = $_SESSION['role'] ?? '';

$isAdminOrHR = ($userRole === 'Admin' || $userRole === 'HR');
$isDepartmentHead = ($userRole === 'Department Head');

// Only Admin, HR, and Department Head can save schedules
if (!$isAdminOrHR && !$isDepartmentHead) {
    header("Location: dashboard.php");
    exit();
}


// ==========================================================
// ONLY POST REQUEST
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: schedule.php");
    exit();
}


// ==========================================================
// GET FORM DATA
// ==========================================================

$employeeId = intval($_POST['employee_id'] ?? 0);
$scheduleDate = $_POST['effective_date'] ?? '';
$timeIn = $_POST['time_in'] ?? '';
$timeOut = $_POST['time_out'] ?? '';


// Break fields are currently disabled in the form
$breakStart = null;
$breakEnd = null;


// ==========================================================
// BASIC VALIDATION
// ==========================================================

if ($employeeId <= 0) {
    die("Please select an employee.");
}

if (empty($scheduleDate)) {
    die("Please select an effective date.");
}

if (empty($timeIn)) {
    die("Please enter Time In.");
}

if (empty($timeOut)) {
    die("Please enter Time Out.");
}


// ==========================================================
// CHECK IF EMPLOYEE EXISTS
// ==========================================================

$employeeCheck = mysqli_prepare(
    $conn,
    "SELECT id FROM employees WHERE id = ? LIMIT 1"
);

if (!$employeeCheck) {
    die("Employee check failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $employeeCheck,
    "i",
    $employeeId
);

mysqli_stmt_execute($employeeCheck);

$employeeResult = mysqli_stmt_get_result($employeeCheck);

if (!$employeeResult || mysqli_num_rows($employeeResult) === 0) {
    mysqli_stmt_close($employeeCheck);
    die("Selected employee does not exist.");
}

mysqli_stmt_close($employeeCheck);


// ==========================================================
// CHECK FOR EXISTING SCHEDULE
// ==========================================================
// Prevent duplicate schedule for the same employee and date.

$duplicateCheck = mysqli_prepare(
    $conn,
    "SELECT id
     FROM schedules
     WHERE employee_id = ?
     AND schedule_date = ?
     LIMIT 1"
);

if (!$duplicateCheck) {
    die("Duplicate check failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $duplicateCheck,
    "is",
    $employeeId,
    $scheduleDate
);

mysqli_stmt_execute($duplicateCheck);

$duplicateResult = mysqli_stmt_get_result($duplicateCheck);

if ($duplicateResult && mysqli_num_rows($duplicateResult) > 0) {
    mysqli_stmt_close($duplicateCheck);

    $_SESSION['schedule_error'] =
        "This employee already has a schedule for this date.";

    header("Location: schedule.php");
    exit();
}

mysqli_stmt_close($duplicateCheck);


// ==========================================================
// INSERT SCHEDULE
// ==========================================================

$sql = "
    INSERT INTO schedules
    (
        employee_id,
        schedule_date,
        time_in,
        time_out,
        break_start,
        break_end
    )
    VALUES (?, ?, ?, ?, ?, ?)
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Schedule insert failed: " . mysqli_error($conn));
}


// employee_id = integer
// everything else = string
mysqli_stmt_bind_param(
    $stmt,
    "isssss",
    $employeeId,
    $scheduleDate,
    $timeIn,
    $timeOut,
    $breakStart,
    $breakEnd
);


// ==========================================================
// EXECUTE
// ==========================================================

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    $_SESSION['schedule_success'] =
        "Schedule created successfully.";

    header("Location: schedule.php");
    exit();

} else {

    $error = mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);

    $_SESSION['schedule_error'] =
        "Failed to create schedule: " . $error;

    header("Location: schedule.php");
    exit();
}
?>