<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/attendance.css">
    <title>Attendance | HR Dashboard</title>
</head>
<body class="dashboard-page"
      data-show-form="<?php echo isset($_SESSION['attendance_error']) ? '1' : '0'; ?>">
    <div class="dashboard-shell">
        <?php include 'sidebar.php'; ?>
        <main class="dashboard-main">
            <div class="dashboard-container">
                <div class="attendance-container">
                    <div class="attendance-content">
                    <?php
                    if (isset($_SESSION['attendance_success'])) {
                        echo '<div class="success-message">' . htmlspecialchars($_SESSION['attendance_success']) . '</div>';
                        unset($_SESSION['attendance_success']);
                    }
                    if (isset($_SESSION['attendance_error'])) {
                        echo '<div class="error-message">' . htmlspecialchars($_SESSION['attendance_error']) . '</div>';
                        unset($_SESSION['attendance_error']);
                    }
                    ?>
                    <!-- Page Header -->
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">Time & Attendance</p>
                            <h1>Attendance</h1>
                        </div>
                        <a href="attendance.php?show_form=1" class="primary-btn" id="recordAttendanceBtn" style="text-decoration:none; display:inline-block;" onclick="window.location.href='attendance.php?show_form=1'; return false;">
                            + Record Attendance
                        </a>
                    </div>
                    <?php
                    $showForm = false;
                    if (isset($_GET['show_form']) && $_GET['show_form'] === '1') {
                        $showForm = true;
                    }
                    ?>
                    <?php if ($showForm): ?>
                        <div class="attendance-form-wrapper">
                            <?php include 'add_attendance.php'; ?>
                        </div>
                    <?php else: ?>
                        <!-- Summary Cards -->
                        <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Present Today</h3>
                            <p>214</p>
                        </div>
                        <div class="summary-card orange">
                            <h3>Late</h3>
                            <p>12</p>
                        </div>
                        <div class="summary-card red">
                            <h3>Absent</h3>
                            <p>8</p>
                        </div>
                        <div class="summary-card purple">
                            <h3>On Leave</h3>
                            <p>14</p>
                        </div>
                    </div>
                        <!-- Attendance Table -->
                        <div class="employee-panel">
                            <div class="panel-header">
                                <h2>Today's Attendance</h2>
                            </div>
                            <table class="dashboard-table employee-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Date</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                include "database.php";
                                $sql = "SELECT attendance.*,
                                            employees.firstname,
                                            employees.lastname,
                                            employees.department
                                        FROM attendance
                                        INNER JOIN employees
                                        ON attendance.employee_id = employees.id
                                        ORDER BY attendance.attendance_date DESC";

                                $result = mysqli_query($conn, $sql);
                                if(mysqli_num_rows($result) > 0){
                                    while($row = mysqli_fetch_assoc($result)){
                                        $initials = strtoupper(
                                            substr($row['firstname'],0,1).
                                            substr($row['lastname'],0,1)
                                        );
                                        // Badge color
                                        $badge = "";
                                        switch($row['status']){

                                            case "Present":
                                                $badge = "present";
                                                break;

                                            case "Late":
                                                $badge = "late";
                                                break;

                                            case "Absent":
                                                $badge = "absent";
                                                break;

                                            case "On Leave":
                                                $badge = "on-leave";
                                                break;

                                            default:
                                                $badge = "pending";

                                        }
                                ?>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar blue-bg">
                                                <?php echo $initials; ?>
                                            </div>
                                            <?php
                                            echo $row['firstname']." ".$row['lastname'];
                                            ?>
                                        </div>
                                    </td>
                                    <td><?php echo $row['department']; ?></td>
                                    <td>
                                        <?php
                                        echo date("F d, Y", strtotime($row['attendance_date']));
                                        ?>
                                    </td>
                                    <td><?php echo $row['time_in']; ?></td>
                                    <td><?php echo $row['time_out']; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $badge; ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="actions-cell">
                                        <form action="employee.php" method="GET" style="display:inline;">
                                            <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="edit-btn" data-employee-id="<?php echo $row['id']; ?>">
                                                ✏️ Edit
                                            </button>
                                        </form>
                                        <button type="button" class="employee-delete-btn"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-name="<?php echo $row['firstname'] . ' ' . $row['lastname']; ?>">
                                            🗑️ Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php
                                    }
                                }else{
                                ?>
                                <tr>
                                    <td colspan="6" style="text-align:center;">
                                        No attendance records found.
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>
