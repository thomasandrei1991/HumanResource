<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/leave_management.css">
    <title>Leave Management | HR Dashboard</title>
</head>
<body class="dashboard-page">
    <div class="dashboard-shell">
        <?php
            session_start();
            if (!isset($_SESSION['user_id'])) {
                header("Location: login.php");
                exit();
            }

            $currentPage = basename($_SERVER['PHP_SELF']);
            $userRole = $_SESSION['role'] ?? '';
            require_once 'database.php';

            // ==========================
            // GET EMPLOYEES
            // ==========================

            $result = mysqli_query($conn, "SELECT id, firstname, lastname
                FROM employees
                ORDER BY firstname ASC, lastname ASC"
            );

        ?>

        <?php include 'sidebar.php'; ?>
        <main class="dashboard-main">
            <div class="dashboard-container">
                <div class="leave-container">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">Employee Leave</p>
                            <h1>Leave Management</h1>
                        </div>
                        <button type="button" class="primary-btn" id="newLeaveBtn">
                            + New Leave
                        </button>   
                    </div>

                    <!-- ==========================
                        LEAVE REQUEST FORM
                    ========================== -->

                    <div id="leaveFormPanel" class="leave-form-wrapper hidden">
                        <div class="panel-header">
                            <h2>File Leave Request</h2>
                        </div>
                        <form action="add_leave_process.php" method="POST" id="leaveForm">

                            <div class="input-group">
                                <label for="employee">Employee</label>
                                <select id="employee" name="employee_id" class="inputs" required>
                                    <option value="" disabled selected>
                                        Select employee
                                    </option>
                                    <?php
                                        if (isset($result) && $result && mysqli_num_rows($result) > 0):
                                            while ($row = mysqli_fetch_assoc($result)):
                                    ?>
                                        <option value="<?php echo htmlspecialchars($row['id']); ?>">
                                            <?php
                                                echo htmlspecialchars($row['firstname'] . ' ' .$row['lastname']);
                                            ?>
                                        </option>
                                        <?php
                                            endwhile;
                                            else:
                                        ?>
                                        <option value="" disabled>No employees available</option>
                                    <?php endif; ?>
                                </select>

                            </div>

                            <div class="input-group">
                                <label for="leaveType">Leave Type</label>
                                <select id="leaveType" name="leave_type" class="inputs" required>
                                    <option value="" disabled selected>
                                        Select leave type
                                    </option>
                                    <option value="Vacation Leave">
                                        Vacation Leave
                                    </option>
                                    <option value="Sick Leave">
                                        Sick Leave
                                    </option>
                                    <option value="Emergency Leave">
                                        Emergency Leave
                                    </option>
                                    <option value="Maternity/Paternity Leave">
                                        Maternity/Paternity Leave
                                    </option>
                                    <option value="Bereavement Leave">
                                        Bereavement Leave
                                    </option>
                                    <option value="Unpaid Leave">
                                        Unpaid Leave
                                    </option>
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="startDate">Start Date</label>
                                <input type="date" id="startDate" name="start_date" class="inputs" required>
                            </div>

                            <div class="input-group">
                                <label for="endDate">End Date</label>
                                <input type="date" id="endDate" name="end_date" class="inputs" required>
                            </div>

                            <div class="input-group">
                                <label for="totalDays">Total Days</label>
                                <input type="number" id="totalDays" name="total_days" class="inputs" placeholder="Automatically calculated" min="1" step="1" readonly required>
                            </div>

                            <div class="input-group">
                                <label for="reason">Reason</label>
                                <textarea id="reason" name="reason" class="inputs" rows="4" placeholder="Briefly explain the reason for this leave request" required></textarea>
                            </div>

                            <!-- New requests are always Pending -->
                            <input type="hidden" name="status" value="Pending">
                            <div class="form-actions">
                                <button type="submit" class="primary-btn">
                                    Save
                                </button>
                                <button type="button" class="primary-btn" id="cancelLeaveBtn">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- Summary Cards -->
                    <div class="employee-summary">
                        <div class="summary-card orange">
                            <h3>Pending</h3>
                            <p>12</p>
                        </div>
                        <div class="summary-card green">
                            <h3>Approved</h3>
                            <p>48</p>
                        </div>
                        <div class="summary-card red">
                            <h3>Rejected</h3>
                            <p>5</p>
                        </div>
                        <div class="summary-card purple">
                            <h3>On Leave</h3>
                            <p>17</p>
                        </div>
                    </div>
                    <!-- Attendance Table -->
                    <div class="employee-panel">
                        <div class="panel-header">
                            <h2>Leave Requests</h2>
                        </div>
                        <table class="dashboard-table employee-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Leave Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar blue-bg">JD</div>
                                            John Doe
                                        </div>
                                    </td>
                                    <td>Vacation Leave</td>
                                    <td>Aug 5, 2026</td>
                                    <td>Aug 9, 2026</td>
                                    <td>5</td>
                                    <td>
                                        <span class="status-badge pending">Pending
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar green-bg">JS</div>
                                            Jane Smith
                                        </div>
                                    </td>
                                    <td>Sick Leave</td>
                                    <td>Jul 30, 2026</td>
                                    <td>Jul 31, 2026</td>
                                    <td>2</td>
                                    <td>
                                        <span class="status-badge present">Approved</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar orange-bg">MR</div>
                                            Mark Reyes
                                        </div>
                                    </td>
                                    <td>Emergency Leave</td>
                                    <td>Jul 28, 2026</td>
                                    <td>Jul 28, 2026</td>
                                    <td>1</td>
                                    <td>
                                        <span class="status-badge absent">
                                            Rejected
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>
