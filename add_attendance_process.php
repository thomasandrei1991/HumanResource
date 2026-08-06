<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: attendance.php');
    exit();
}

$employeeId = trim($_POST['employee_id'] ?? '');
$attendanceDate = trim($_POST['attendance_date'] ?? '');
$timeIn = trim($_POST['time_in'] ?? '');
$timeOut = trim($_POST['time_out'] ?? '');
$status = trim($_POST['status'] ?? '');

if ($employeeId === '' || $attendanceDate === '' || $status === '') {
    $_SESSION['attendance_error'] = 'Please select an employee, date, and status.';
    header('Location: attendance.php');
    exit();
}

$timeInValue = $timeIn === '' ? null : $timeIn;
$timeOutValue = $timeOut === '' ? null : $timeOut;

$sql = "INSERT INTO attendance (employee_id, attendance_date, time_in, time_out, status) VALUES ('$employeeId', '$attendanceDate', " . ($timeInValue === null ? 'NULL' : "'$timeInValue'") . ", " . ($timeOutValue === null ? 'NULL' : "'$timeOutValue'") . ", '$status')";

if (mysqli_query($conn, $sql)) {
    $_SESSION['attendance_success'] = 'Attendance recorded successfully.';
} else {
    $_SESSION['attendance_error'] = 'Failed to save attendance.';
}

header('Location: attendance.php');
exit();
