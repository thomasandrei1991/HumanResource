<?php
session_start();

// Siguraduhing naka-login at Admin ang nagdo-ddelete
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header("Location: login.php");
    exit();
}

require_once 'database.php';

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    // Burahin ang record sa users table
    $query = "DELETE FROM users WHERE id = $userId AND role = 'Department Head'";
    
    if (mysqli_query($conn, $query)) {
        // Opsyonal: Kung gusto mong i-clear din ang department_head column sa departments table:
        // mysqli_query($conn, "UPDATE departments SET department_head = NULL WHERE department_head = (SELECT fullname FROM users WHERE id = $userId)");
    }
}

// Bumalik sa listahan ng Department Heads
header("Location: department_heads.php");
exit();
?>