<?php
session_start();
include "database.php";

// ==========================
// GET LOGIN DATA
// ==========================
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// ==========================
// VALIDATE INPUT
// ==========================
if ($username === '' || $password === '') {
    $_SESSION['login_error'] = "Please enter username and password.";
    header("Location: login.php");
    exit();
}

// ==========================
// FIND USER IN USERS TABLE
// ==========================
$sql  = "SELECT * FROM users WHERE username = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) !== 1) {
    $_SESSION['login_error'] = "Invalid username or password.";
    header("Location: login.php");
    exit();
}

$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// ==========================
// VERIFY PASSWORD
// ==========================
if (password_verify($password, $user['password'])) {

    // 1. I-set ang pangunahing user session
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role'] ?? 'Employee';

    // Default profile values
    $_SESSION['employee_id'] = $user['id']; // Fallback: gagamitin ang users.id
    $_SESSION['fullname']    = $user['fullname'] ?? $user['username'];
    $_SESSION['firstname']   = '';
    $_SESSION['lastname']    = '';
    $_SESSION['email']       = '';
    $_SESSION['phone']       = '';
    $_SESSION['department']  = '';
    $_SESSION['position']    = '';

    // 2. Hanapin sa EMPLOYEES table gamit ang tamang employee_id link
    if (!empty($user['employee_id'])) {
        $empStmt = mysqli_prepare(
            $conn, 
            "SELECT id, firstname, lastname, email, phone, department, position 
             FROM employees 
             WHERE id = ?
             LIMIT 1"
        );

        if ($empStmt) {
            mysqli_stmt_bind_param($empStmt, "i", $user['employee_id']);
            mysqli_stmt_execute($empStmt);
            $empRes = mysqli_stmt_get_result($empStmt);

            if ($emp = mysqli_fetch_assoc($empRes)) {
                $_SESSION['employee_id'] = $emp['id'];
                $_SESSION['firstname']   = $emp['firstname'];
                $_SESSION['lastname']    = $emp['lastname'];
                $_SESSION['fullname']    = trim($emp['firstname'] . ' ' . $emp['lastname']);
                $_SESSION['email']       = $emp['email'];
                $_SESSION['phone']       = $emp['phone'];
                $_SESSION['department']  = $emp['department'];
                $_SESSION['position']    = $emp['position'];
            }
            mysqli_stmt_close($empStmt);
        }
    }

    // 3. ROLE-BASED REDIRECTION
    $role = strtolower(trim($_SESSION['role']));
    
    if ($role === 'employee') {
        header("Location: employee_dashboard.php");
    } elseif ($role === 'department head' || $role === 'dept head') {
        header("Location: department_head_dashboard.php");
    } elseif ($role === 'admin' || $role === 'hr') {
        header("Location: dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();

} else {
    // Mali ang password
    $_SESSION['login_error'] = "Invalid username or password.";
    header("Location: login.php");
    exit();
}
?>