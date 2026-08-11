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
// FIND USER
// ==========================

$sql = "
    SELECT *
    FROM users
    WHERE username = ?
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $username
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


// ==========================
// CHECK USER
// ==========================

if (mysqli_num_rows($result) !== 1) {

    $_SESSION['login_error'] = "Invalid username or password.";

    header("Location: login.php");
    exit();

}


// ==========================
// GET USER
// ==========================

$user = mysqli_fetch_assoc($result);


// ==========================
// VERIFY PASSWORD
// ==========================

if (!password_verify($password, $user['password'])) {

    $_SESSION['login_error'] = "Invalid username or password.";

    header("Location: login.php");
    exit();

}


// ==========================
// CREATE SESSION
// ==========================

$_SESSION['user_id'] = $user['id'];

$_SESSION['employee_id'] = $user['employee_id'];

$_SESSION['username'] = $user['username'];

$_SESSION['fullname'] = $user['fullname'];

$_SESSION['role'] = $user['role'];


// ==========================
// ROLE-BASED REDIRECTION
// ==========================

if ($user['role'] === 'Admin') {

    header("Location: dashboard.php");

} elseif ($user['role'] === 'HR') {

    header("Location: dashboard.php");

} elseif ($user['role'] === 'Department Head') {

    header("Location: department_head_dashboard.php");

} elseif ($user['role'] === 'Employee') {

    header("Location: employee_dashboard.php");

} else {

    $_SESSION['login_error'] = "Invalid user role.";

    header("Location: login.php");

}

exit();

?>