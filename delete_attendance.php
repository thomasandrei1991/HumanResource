<?php

    session_start();
    include "database.php";

    /*
    |--------------------------------------------------------------------------
    | LOGIN CHECK
    |--------------------------------------------------------------------------
    */

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | ROLE CHECK
    |--------------------------------------------------------------------------
    |
    | Only Admin and HR can delete attendance records.
    |
    */

    $role = $_SESSION['role'] ?? '';

    if ($role !== 'Admin' && $role !== 'HR') {
        http_response_code(403);
        die("Access denied.");
    }

    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE ID CHECK
    |--------------------------------------------------------------------------
    */

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        die("Attendance ID is missing.");
    }

    $id = intval($_GET['id']);

    /*
    |--------------------------------------------------------------------------
    | DELETE ATTENDANCE
    |--------------------------------------------------------------------------
    */

    $sql = "DELETE FROM attendance WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: attendance.php?success=deleted");
        exit();
    } else {
        die("SQL Error: " .mysqli_error($conn));
    }

?>


