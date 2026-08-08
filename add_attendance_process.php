<?php
    // Start (or resume) the PHP session so we can use $_SESSION to pass messages back to attendance.php
    session_start();

    // Load the database connection file, which sets up the $conn variable (mysqli connection)
    include 'database.php';

    // Only allow this script to run if the request came in as a POST (i.e. from a form submission)
    // This prevents people from just visiting this file directly in their browser
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: attendance.php'); // send them back to the attendance page
        exit(); // stop the script immediately so nothing below runs
    }

    // Grab each form field from $_POST, trim whitespace, and default to '' if not set
    // The ?? '' is the "null coalescing operator" — if $_POST['employee_id'] doesn't exist, use '' instead
    $employeeId = trim($_POST['employee_id'] ?? '');
    $attendanceDate = trim($_POST['attendance_date'] ?? '');
    $timeIn = trim($_POST['time_in'] ?? '');
    $timeOut = trim($_POST['time_out'] ?? '');
    $status = trim($_POST['status'] ?? '');

    // Basic validation: employee, date, and status are required fields
    // time_in / time_out are allowed to be empty (optional)
    if ($employeeId === '' || $attendanceDate === '' || $status === '') {
        // Store an error message in the session so attendance.php can display it after redirect
        $_SESSION['attendance_error'] = 'Please select an employee, date, and status.';
        header('Location: attendance.php');
        exit();
    }

    // If time_in / time_out were left blank, convert them to PHP null
    // so we can later insert SQL NULL instead of an empty string
    $timeInValue = $timeIn === '' ? null : $timeIn;
    $timeOutValue = $timeOut === '' ? null : $timeOut;

    // Build the SQL INSERT statement
    // Note: time_in/time_out use a ternary to write the literal word NULL (no quotes)
    // when the value is null, or a quoted string when there's an actual time value
    $sql = "INSERT INTO attendance (employee_id, attendance_date, time_in, time_out, status) VALUES ('$employeeId', '$attendanceDate', " . ($timeInValue === null ? 'NULL' : "'$timeInValue'") . ", " . ($timeOutValue === null ? 'NULL' : "'$timeOutValue'") . ", '$status')";

    // Run the query. mysqli_query returns true/false depending on success
    if (mysqli_query($conn, $sql)) {
        // Success message stored in session for the redirected page to show
        $_SESSION['attendance_success'] = 'Attendance recorded successfully.';
    } else {
        // Query failed (e.g. bad SQL, DB connection issue, constraint violation)
        $_SESSION['attendance_error'] = 'Failed to save attendance.';
    }

    // Redirect back to attendance.php either way (this is the Post/Redirect/Get pattern —
    // it stops the browser from re-submitting the form if the user refreshes the page)
    header('Location: attendance.php');
    exit();
?>