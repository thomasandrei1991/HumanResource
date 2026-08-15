<?php
    session_start();
    require_once 'database.php';

    // ==========================================================
    // LOGIN CHECK
    // ==========================================================

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // ==========================================================
    // CURRENT USER
    // ==========================================================

    $userId = intval($_SESSION['user_id']);
    $userRole = $_SESSION['role'] ?? '';

    // ==========================================================
    // GET EMPLOYEE ID OF LOGGED-IN USER
    // ==========================================================

    $employeeId = null;

    $userStmt = mysqli_prepare(
        $conn,
        "SELECT employee_id
        FROM users
        WHERE id = ?
        LIMIT 1"
    );

    if ($userStmt) {

        mysqli_stmt_bind_param($userStmt, "i", $userId);
        mysqli_stmt_execute($userStmt);

        $userResult = mysqli_stmt_get_result($userStmt);

        if ($userData = mysqli_fetch_assoc($userResult)) {
            $employeeId = $userData['employee_id'];
        }

        mysqli_stmt_close($userStmt);
    }

    // ==========================================================
    // GET EMPLOYEE INFORMATION
    // ==========================================================

    $employee = null;

    if ($employeeId) {

        $employeeStmt = mysqli_prepare(
            $conn,
            "SELECT
                id,
                employee_id,
                firstname,
                lastname,
                department,
                position
            FROM employees
            WHERE id = ?
            LIMIT 1"
        );

        if ($employeeStmt) {

            mysqli_stmt_bind_param($employeeStmt, "i", $employeeId);
            mysqli_stmt_execute($employeeStmt);

            $employeeResult = mysqli_stmt_get_result($employeeStmt);

            if ($employeeResult) {
                $employee = mysqli_fetch_assoc($employeeResult);
            }

            mysqli_stmt_close($employeeStmt);
        }
    }

    // ==========================================================
    // GET EMPLOYEE SCHEDULES
    // ==========================================================

    $schedules = [];

    if ($employeeId) {

        $scheduleStmt = mysqli_prepare(
            $conn,
            "SELECT
                id,
                schedule_date,
                time_in,
                time_out,
                break_start,
                break_end,
                created_at
            FROM schedules
            WHERE employee_id = ?
            ORDER BY schedule_date ASC"
        );

        if ($scheduleStmt) {

            mysqli_stmt_bind_param($scheduleStmt, "i", $employeeId);
            mysqli_stmt_execute($scheduleStmt);

            $scheduleResult = mysqli_stmt_get_result($scheduleStmt);

            if ($scheduleResult) {

                while ($row = mysqli_fetch_assoc($scheduleResult)) {
                    $schedules[] = $row;
                }
            }

            mysqli_stmt_close($scheduleStmt);
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link
        rel="stylesheet"
        href="styles/common.css"
    >

    <link
        rel="stylesheet"
        href="styles/my_schedule.css"
    >

    <title>My Schedule | HR Dashboard</title>

</head>

<body class="dashboard-page">

<div class="dashboard-shell">

    <?php include 'sidebar.php'; ?>

    <main class="dashboard-main">

        <div class="dashboard-container">

            <div class="my-schedule-container">

                <!-- PAGE HEADER -->

                <div class="page-header">

                    <div>

                        <p class="page-kicker">
                            Workforce Management
                        </p>

                        <h1>
                            My Schedule
                        </h1>

                    </div>

                </div>


                <!-- EMPLOYEE INFORMATION -->

                <?php if ($employee): ?>

                    <div class="employee-info-card">

                        <div class="employee-avatar">

                            <?php
                            $initials =
                                strtoupper(
                                    substr($employee['firstname'], 0, 1) .
                                    substr($employee['lastname'], 0, 1)
                                );

                            echo htmlspecialchars($initials);
                            ?>

                        </div>


                        <div class="employee-details">

                            <h2>

                                <?php
                                echo htmlspecialchars(
                                    $employee['firstname'] .
                                    ' ' .
                                    $employee['lastname']
                                );
                                ?>

                            </h2>

                            <p>
                                Employee ID:
                                <?php
                                echo htmlspecialchars(
                                    $employee['employee_id']
                                );
                                ?>
                            </p>

                            <p>
                                Department:
                                <?php
                                echo htmlspecialchars(
                                    $employee['department']
                                );
                                ?>
                            </p>

                            <p>
                                Position:
                                <?php
                                echo htmlspecialchars(
                                    $employee['position']
                                );
                                ?>
                            </p>

                        </div>

                    </div>

                <?php endif; ?>


                <!-- SCHEDULE TABLE -->

                <div class="employee-panel">

                    <div class="panel-header">

                        <div>

                            <p class="page-kicker">
                                Work Schedule
                            </p>

                            <h2>
                                My Work Schedule
                            </h2>

                        </div>

                    </div>


                    <div class="table-wrapper">

                        <table class="dashboard-table">

                            <thead>

                                <tr>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Time In
                                    </th>

                                    <th>
                                        Time Out
                                    </th>

                                    <th>
                                        Break Start
                                    </th>

                                    <th>
                                        Break End
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php if (!empty($schedules)): ?>

                                    <?php foreach ($schedules as $schedule): ?>

                                        <tr>

                                            <!-- DATE -->

                                            <td>

                                                <?php

                                                if (!empty($schedule['schedule_date'])) {

                                                    echo htmlspecialchars(
                                                        date(
                                                            "F d, Y",
                                                            strtotime(
                                                                $schedule['schedule_date']
                                                            )
                                                        )
                                                    );

                                                } else {

                                                    echo "-";

                                                }

                                                ?>

                                            </td>


                                            <!-- TIME IN -->

                                            <td>

                                                <?php

                                                if (!empty($schedule['time_in'])) {

                                                    echo htmlspecialchars(
                                                        date(
                                                            "h:i A",
                                                            strtotime(
                                                                $schedule['time_in']
                                                            )
                                                        )
                                                    );

                                                } else {

                                                    echo "-";

                                                }

                                                ?>

                                            </td>


                                            <!-- TIME OUT -->

                                            <td>

                                                <?php

                                                if (!empty($schedule['time_out'])) {

                                                    echo htmlspecialchars(
                                                        date(
                                                            "h:i A",
                                                            strtotime(
                                                                $schedule['time_out']
                                                            )
                                                        )
                                                    );

                                                } else {

                                                    echo "-";

                                                }

                                                ?>

                                            </td>


                                            <!-- BREAK START -->

                                            <td>

                                                <?php

                                                if (!empty($schedule['break_start'])) {

                                                    echo htmlspecialchars(
                                                        date(
                                                            "h:i A",
                                                            strtotime(
                                                                $schedule['break_start']
                                                            )
                                                        )
                                                    );

                                                } else {

                                                    echo "-";

                                                }

                                                ?>

                                            </td>


                                            <!-- BREAK END -->

                                            <td>

                                                <?php

                                                if (!empty($schedule['break_end'])) {

                                                    echo htmlspecialchars(
                                                        date(
                                                            "h:i A",
                                                            strtotime(
                                                                $schedule['break_end']
                                                            )
                                                        )
                                                    );

                                                } else {

                                                    echo "-";

                                                }

                                                ?>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>

                                        <td
                                            colspan="5"
                                            style="text-align: center;"
                                        >

                                            No schedule has been assigned to you yet.

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>


            </div>

        </div>

    </main>

</div>

</body>

</html>