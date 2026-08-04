<?php
session_start();
include "database.php";

$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirmPassword = trim($_POST['confirmPassword'] ?? '');

if ($fullname === '' || $username === '' || $password === '' || $confirmPassword === '') {
    $_SESSION['register_error'] = 'Please complete all fields.';
    header('Location: register.php');
    exit();
}

if ($password !== $confirmPassword) {
    $_SESSION['register_error'] = 'Passwords do not match.';
    header('Location: register.php');
    exit();
}

if (strlen($password) < 5) {
    $_SESSION['register_error'] = 'Password must be at least 5 characters long.';
    header('Location: register.php');
    exit();
}

$checkSql = "SELECT id FROM users WHERE username = '$username'";
$checkResult = mysqli_query($conn, $checkSql);

if (mysqli_num_rows($checkResult) > 0) {
    $_SESSION['register_error'] = 'Username already exists.';
    header('Location: register.php');
    exit();
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$insertSql = "INSERT INTO users (fullname, username, password, role) VALUES ('$fullname', '$username', '$hashedPassword', 'Employee')";

if (mysqli_query($conn, $insertSql)) {
    $_SESSION['register_success'] = 'Account created successfully. Please sign in.';
    header('Location: login.php');
    exit();
} else {
    $_SESSION['register_error'] = 'Registration failed. Please try again.';
    header('Location: register.php');
    exit();
}
?>
