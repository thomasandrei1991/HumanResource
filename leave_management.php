<?php

    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    require_once 'database.php';

    $currentPage = basename($_SERVER['PHP_SELF']);
    $userRoleRaw = $_SESSION['role'] ?? '';
    $userRole = trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $userRoleRaw));
    $userId = intval($_SESSION['user_id']);

    $isDepartmentHead = (strcasecmp($userRole, 'Department Head') === 0);
    $isEmployee = (strcasecmp($userRole, 'Employee') === 0);


    // ==========================================================
    // ACCESS CHECK
    // ==========================================================

    if ($userRole !== 'Employee' && $userRole !== 'Department Head') {
        header("Location: dashboard.php");
        exit();
    }


    // ==========================================================
    // VARIABLES
    // ==========================================================

    $employeeId = null;
    $departmentName = '';


    // ==========================================================
    // EMPLOYEE ACCOUNT
    // ==========================================================

    if ($userRole === 'Employee') {

        /*
        * users.employee_id points to employees.id
        */
        $userStmt = mysqli_prepare($conn, "SELECT employee_id FROM users WHERE id = ? LIMIT 1");

        if ($userStmt) {
            mysqli_stmt_bind_param($userStmt, "i", $userId);
            mysqli_stmt_execute($userStmt);
            $userResult = mysqli_stmt_get_result($userStmt);
            if ($userData = mysqli_fetch_assoc($userResult)) {
                $employeeId = intval($userData['employee_id']);
            }

            mysqli_stmt_close($userStmt);
        }


        if (!$employeeId) {
            die("Employee account is not properly linked to an employee record.");
        }
    }


    // ==========================================================
    // DEPARTMENT HEAD ACCOUNT
    // ==========================================================

    if ($userRole === 'Department Head') {
        $headName = $_SESSION['fullname'] ?? '';
        /*
        * Find the department assigned to this Department Head
        */
        $deptStmt = mysqli_prepare(
            $conn,
            "SELECT department_name
            FROM departments
            WHERE department_head = ?
            AND status = 'Active'
            LIMIT 1"
        );

        if ($deptStmt) {
            mysqli_stmt_bind_param($deptStmt, "s", $headName);
            mysqli_stmt_execute($deptStmt);
            $deptResult = mysqli_stmt_get_result($deptStmt);
            if ($deptData = mysqli_fetch_assoc($deptResult)) {
                $departmentName = $deptData['department_name'] ?? '';
            }

            mysqli_stmt_close($deptStmt);
        }


        if (empty($departmentName)) {
            die("No active department is assigned to this Department Head.");
        }
    }


    // ==========================================================
    // GET LEAVE REQUESTS
    // ==========================================================

    $leaveSql = "
        SELECT
            leave_requests.id,
            leave_requests.employee_id,
            leave_requests.leave_type,
            leave_requests.start_date,
            leave_requests.end_date,
            leave_requests.total_days,
            leave_requests.reason,
            leave_requests.status,
            leave_requests.created_at,

            employees.firstname,
            employees.lastname,
            employees.department

        FROM leave_requests

        INNER JOIN employees
            ON leave_requests.employee_id = employees.id
    ";


    // ==========================================================
    // EMPLOYEE FILTER
    // ==========================================================

    if ($userRole === 'Employee') {
        $leaveSql .= " WHERE leave_requests.employee_id = ? ";
        $leaveSql .= "ORDER BY leave_requests.created_at DESC";
        $leaveStmt = mysqli_prepare($conn, $leaveSql);
        mysqli_stmt_bind_param($leaveStmt, "i", $employeeId);
        mysqli_stmt_execute($leaveStmt);
        $leaveResult = mysqli_stmt_get_result($leaveStmt);
    }


    // ==========================================================
    // DEPARTMENT HEAD FILTER
    // ==========================================================

    else {
        $leaveSql .= " WHERE LOWER(employees.department) = LOWER(?) ";
        $leaveSql .= "ORDER BY leave_requests.created_at DESC";
        $leaveStmt = mysqli_prepare($conn, $leaveSql);
        mysqli_stmt_bind_param($leaveStmt, "s", $departmentName);
        mysqli_stmt_execute($leaveStmt);
        $leaveResult = mysqli_stmt_get_result($leaveStmt);
    }


    // ==========================================================
    // SUMMARY COUNTS
    // ==========================================================

    $pendingCount = 0;
    $approvedCount = 0;
    $rejectedCount = 0;
    $onLeaveCount = 0;


    // Base summary query
    $summarySql = "
        SELECT
            SUM(status = 'Pending') AS pending_count,
            SUM(status = 'Approved') AS approved_count,
            SUM(status = 'Rejected') AS rejected_count
        FROM leave_requests
    ";


    // Employee summary
    if ($userRole === 'Employee') {
        $summarySql .= " WHERE employee_id = ?";
        $summaryStmt = mysqli_prepare($conn, $summarySql);
        mysqli_stmt_bind_param($summaryStmt, "i", $employeeId);
    }


    // Department Head summary
    else {
        $summarySql .= " INNER JOIN employees ON leave_requests.employee_id = employees.id WHERE LOWER(employees.department) = LOWER(?)";
        $summaryStmt = mysqli_prepare($conn, $summarySql);
        mysqli_stmt_bind_param($summaryStmt, "s", $departmentName);
    }


    if ($summaryStmt) {
        mysqli_stmt_execute($summaryStmt);
        $summaryResult = mysqli_stmt_get_result($summaryStmt);
        if ($summaryData = mysqli_fetch_assoc($summaryResult)) {
            $pendingCount = intval($summaryData['pending_count'] ?? 0);
            $approvedCount = intval($summaryData['approved_count'] ?? 0);
            $rejectedCount = intval($summaryData['rejected_count'] ?? 0);
        }
        mysqli_stmt_close($summaryStmt);
    }


    // ==========================================================
    // ON LEAVE COUNT
    // ==========================================================

    $today = date('Y-m-d');
    $onLeaveSql = "SELECT COUNT(*) AS on_leave_count FROM leave_requests";

    if ($userRole === 'Employee') {
        $onLeaveSql .= " WHERE employee_id = ? AND status = 'Approved' AND ? BETWEEN start_date AND end_date";
        $onLeaveStmt = mysqli_prepare($conn, $onLeaveSql);
        mysqli_stmt_bind_param($onLeaveStmt, "is", $employeeId, $today);

    } else {

        $onLeaveSql .= " INNER JOIN employees ON leave_requests.employee_id = employees.id
            WHERE LOWER(employees.department) = LOWER(?)
            AND leave_requests.status = 'Approved'
            AND ? BETWEEN leave_requests.start_date
            AND leave_requests.end_date
        ";

        $onLeaveStmt = mysqli_prepare(
            $conn,
            $onLeaveSql
        );

        mysqli_stmt_bind_param(
            $onLeaveStmt,
            "ss",
            $departmentName,
            $today
        );
    }


    if ($onLeaveStmt) {
        mysqli_stmt_execute($onLeaveStmt);
        $onLeaveResult = mysqli_stmt_get_result($onLeaveStmt);

        if ($onLeaveData = mysqli_fetch_assoc($onLeaveResult)) {
            $onLeaveCount = intval($onLeaveData['on_leave_count'] ?? 0);
        }

        mysqli_stmt_close($onLeaveStmt);
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
        href="styles/leave_management.css"
    >

    <title>Leave Management | HR Dashboard</title>

</head>


<body class="dashboard-page">

<div class="dashboard-shell">

    <?php include 'sidebar.php'; ?>


    <main class="dashboard-main">

        <div class="dashboard-container">

            <div class="leave-container">


                <!-- ==================================================
                     PAGE HEADER
                =================================================== -->

                <div class="page-header">
                    <div>
                        <p class="page-kicker">Employee Leave</p>
                        <h1>Leave Management</h1>
                    </div>

                    <?php if (isset($userRole) && strcasecmp(trim($userRole), 'Employee') === 0): ?>
                        <button type="button" class="primary-btn" id="newLeaveBtn">
                            + New Leave
                        </button>
                    <?php endif; ?>
                </div>


                <!-- ==================================================
                     DEPARTMENT HEAD INFORMATION
                =================================================== -->

                <?php if ($userRole === 'Department Head'): ?>

                    <div class="employee-panel">

                        <div class="panel-header">

                            <h2>
                                <?php echo htmlspecialchars($departmentName); ?>
                                Leave Requests
                            </h2>

                        </div>

                    </div>

                <?php endif; ?>


                <!-- ==================================================
                     EMPLOYEE LEAVE FORM
                =================================================== -->

                <?php include 'leave_request.php'; ?>

                


                <!-- ==================================================
                     SUMMARY
                =================================================== -->

                <div class="employee-summary leave-content">

                    <div class="summary-card orange">

                        <h3>
                            Pending
                        </h3>

                        <p>
                            <?php echo $pendingCount; ?>
                        </p>

                    </div>


                    <div class="summary-card green">

                        <h3>
                            Approved
                        </h3>

                        <p>
                            <?php echo $approvedCount; ?>
                        </p>

                    </div>


                    <div class="summary-card red">

                        <h3>
                            Rejected
                        </h3>

                        <p>
                            <?php echo $rejectedCount; ?>
                        </p>

                    </div>


                    <div class="summary-card purple">

                        <h3>
                            On Leave
                        </h3>

                        <p>
                            <?php echo $onLeaveCount; ?>
                        </p>

                    </div>

                </div>


                <!-- ==================================================
                     LEAVE REQUEST TABLE
                =================================================== -->

                <div class="employee-panel leave-content">

                    <div class="panel-header">

                        <h2>
                            <?php
                            echo $userRole === 'Employee'
                                ? 'My Leave Requests'
                                : 'Leave Requests';
                            ?>
                        </h2>

                    </div>


                    <table class="dashboard-table employee-table">

                        <thead>

                            <tr>

                                <?php if ($userRole === 'Department Head'): ?>

                                    <th>
                                        Employee
                                    </th>

                                <?php endif; ?>

                                <th>
                                    Leave Type
                                </th>

                                <th>
                                    Date From
                                </th>

                                <th>
                                    Date To
                                </th>

                                <th>
                                    Days
                                </th>

                                <th>
                                    Reason
                                </th>

                                <th>
                                    Status
                                </th>

                                <?php if ($userRole === 'Department Head'): ?>

                                    <th>
                                        Actions
                                    </th>

                                <?php endif; ?>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if ($leaveResult && mysqli_num_rows($leaveResult) > 0): ?>

                            <?php while ($leave = mysqli_fetch_assoc($leaveResult)): ?>

                                <tr>


                                    <!-- EMPLOYEE NAME -->
                                    <?php if ($userRole === 'Department Head'): ?>

                                        <td>

                                            <div class="employee-name">

                                                <?php
                                                $firstName =
                                                    $leave['firstname'] ?? '';

                                                $lastName =
                                                    $leave['lastname'] ?? '';

                                                $initials =
                                                    strtoupper(
                                                        substr($firstName, 0, 1) .
                                                        substr($lastName, 0, 1)
                                                    );
                                                ?>

                                                <div class="emp-avatar blue-bg">

                                                    <?php
                                                    echo htmlspecialchars($initials);
                                                    ?>

                                                </div>

                                                <?php
                                                echo htmlspecialchars(
                                                    $firstName . ' ' . $lastName
                                                );
                                                ?>

                                            </div>

                                        </td>

                                    <?php endif; ?>


                                    <!-- LEAVE TYPE -->

                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $leave['leave_type']
                                        );
                                        ?>

                                    </td>


                                    <!-- START DATE -->

                                    <td>

                                        <?php
                                        echo date(
                                            "M d, Y",
                                            strtotime($leave['start_date'])
                                        );
                                        ?>

                                    </td>


                                    <!-- END DATE -->

                                    <td>

                                        <?php
                                        echo date(
                                            "M d, Y",
                                            strtotime($leave['end_date'])
                                        );
                                        ?>

                                    </td>


                                    <!-- DAYS -->

                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $leave['total_days']
                                        );
                                        ?>

                                    </td>


                                    <!-- REASON -->

                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $leave['reason']
                                        );
                                        ?>

                                    </td>


                                    <!-- STATUS -->

                                    <td>

                                        <?php

                                        $status =
                                            $leave['status'] ?? 'Pending';

                                        $statusClass = '';

                                        if ($status === 'Pending') {
                                            $statusClass = 'pending';
                                        }

                                        elseif ($status === 'Approved') {
                                            $statusClass = 'approved';
                                        }

                                        elseif ($status === 'Rejected') {
                                            $statusClass = 'absent';
                                        }

                                        ?>

                                        <span
                                            class="status-badge <?php
                                                echo $statusClass;
                                            ?>"
                                        >

                                            <?php
                                            echo htmlspecialchars($status);
                                            ?>

                                        </span>

                                    </td>


                                    <!-- ACTIONS -->

                                    <?php if ($userRole === 'Department Head'): ?>

                                        <td>

                                            <?php if ($status === 'Pending'): ?>

                                                <button
                                                    type="button"
                                                    class="primary-btn"
                                                    onclick="approveLeave(<?php echo $leave['id']; ?>)"
                                                >
                                                    Approve
                                                </button>


                                                <button
                                                    type="button"
                                                    class="primary-btn"
                                                    onclick="rejectLeave(<?php echo $leave['id']; ?>)"
                                                >
                                                    Reject
                                                </button>

                                            <?php else: ?>

                                                <span>
                                                    —
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                    <?php endif; ?>


                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="<?php
                                        echo $userRole === 'Department Head'
                                            ? '8'
                                            : '6';
                                    ?>"
                                    style="text-align:center;"
                                >

                                    No leave requests found.

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>


            </div>

        </div>

    </main>

</div>


<script>

/*
 * ==========================================================
 * EMPLOYEE LEAVE FORM
 * ==========================================================
 */

document.addEventListener('DOMContentLoaded', function () {

    const newLeaveBtn =
        document.getElementById('newLeaveBtn');

    const cancelLeaveBtn =
        document.getElementById('cancelLeaveBtn');

    const leaveFormPanel =
        document.getElementById('leaveFormPanel');


    if (newLeaveBtn && leaveFormPanel) {

        newLeaveBtn.addEventListener('click', function () {

            leaveFormPanel.classList.remove('hidden');

            leaveFormPanel.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

        });

    }


    if (cancelLeaveBtn && leaveFormPanel) {

        cancelLeaveBtn.addEventListener('click', function () {

            leaveFormPanel.classList.add('hidden');

        });

    }


    /*
     * ======================================================
     * AUTO CALCULATE TOTAL DAYS
     * ======================================================
     */

    const startDate =
        document.getElementById('startDate');

    const endDate =
        document.getElementById('endDate');

    const totalDays =
        document.getElementById('totalDays');


    function calculateLeaveDays() {

        if (!startDate || !endDate || !totalDays) {
            return;
        }

        if (!startDate.value || !endDate.value) {

            totalDays.value = '';

            return;
        }


        const start =
            new Date(startDate.value);

        const end =
            new Date(endDate.value);


        if (end < start) {

            totalDays.value = '';

            return;
        }


        const difference =
            end.getTime() - start.getTime();


        const days =
            Math.floor(
                difference / (1000 * 60 * 60 * 24)
            ) + 1;


        totalDays.value = days;

    }


    if (startDate) {
        startDate.addEventListener(
            'change',
            calculateLeaveDays
        );
    }


    if (endDate) {
        endDate.addEventListener(
            'change',
            calculateLeaveDays
        );
    }

});


/*
 * ==========================================================
 * APPROVE / REJECT
 * ==========================================================
 *
 * Hindi pa muna natin ikinakabit sa processor.
 * Placeholder muna ito para ma-test natin ang UI.
 *
 */

function approveLeave(id) {

    if (!confirm("Are you sure you want to approve this leave request?")) {
        return;
    }

    submitLeaveStatus(id, "Approved");
}

function rejectLeave(id) {

    if (!confirm("Are you sure you want to reject this leave request?")) {
        return;
    }

    submitLeaveStatus(id, "Rejected");
}

function submitLeaveStatus(id, status) {

    const form = document.createElement("form");

    form.method = "POST";
    form.action = "update_leave_status.php";

    const leaveIdInput = document.createElement("input");
    leaveIdInput.type = "hidden";
    leaveIdInput.name = "leave_id";
    leaveIdInput.value = id;

    const statusInput = document.createElement("input");
    statusInput.type = "hidden";
    statusInput.name = "status";
    statusInput.value = status;

    form.appendChild(leaveIdInput);
    form.appendChild(statusInput);

    document.body.appendChild(form);

    form.submit();
}

</script>


</body>

</html>