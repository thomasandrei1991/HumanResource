<?php

include "database.php";

$id = $_POST['id'];

$employee_id = $_POST['employee_id'];
$attendance_date = $_POST['attendance_date'];
$time_in = $_POST['time_in'];
$time_out = $_POST['time_out'];
$status = $_POST['status'];

$sql = "UPDATE attendance SET

employee_id='$employee_id',
attendance_date='$attendance_date',
time_in='$time_in',
time_out='$time_out',
status='$status'

WHERE id='$id'";

if(mysqli_query($conn,$sql)){

    header("Location: attendance.php?success=updated");
    exit();

}else{

    die(mysqli_error($conn));

}

?>