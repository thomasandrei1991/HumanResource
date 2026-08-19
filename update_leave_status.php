<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'database.php';

// ==========================
// GET REQUEST DATA
// ==========================

$leaveId = intval($_POST['leave_id'] ?? 0);
$status = $_POST['status'] ?? '';

// ==========================
// VALIDATE INPUT
// ==========================

if (
    $leaveId <= 0 ||
    !in_array($status, ['Approved', 'Rejected'], true)
) {
    header("Location: leave_management.php?error=invalid");
    exit();
}

// ==========================
// UPDATE LEAVE STATUS
// ==========================

$stmt = mysqli_prepare(
    $conn,
    "UPDATE leave_requests
     SET status = ?
     WHERE id = ?
     AND status = 'Pending'"
);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $status,
    $leaveId
);

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header("Location: leave_management.php?success=status_updated");
    exit();
}

mysqli_stmt_close($stmt);

header("Location: leave_management.php?error=update_failed");
exit();

?>