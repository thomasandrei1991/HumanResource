<?php
session_start();
include "database.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$sql = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {

    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit();

    } else {

        $_SESSION['login_error'] = "Invalid username or password.";
        header("Location: login.php");
        exit();

    }

} else {

    $_SESSION['login_error'] = "Invalid username or password.";
    header("Location: login.php");
    exit();

}
?>