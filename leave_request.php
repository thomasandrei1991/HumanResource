<?php
// Load the database connection ($conn) from database.php
include 'database.php';

// Query to get every employee's id, firstname, and lastname,
// sorted alphabetically by firstname — used to populate the
// employee dropdown below
$sql = "SELECT id, firstname, lastname FROM employees ORDER BY firstname ASC";

// Run the query and store the result set (a mysqli_result object) in $result
$result = mysqli_query($conn, $sql);
?>

<!-- ==========================
     LEAVE REQUEST FORM
     Hidden by default ("hidden" class) — likely toggled visible via
     JS (e.g. a "File Leave Request" button, similar to the pattern
     used for the attendance and employee forms)
========================== -->
<div id="leaveFormPanel" class="leave-form-wrapper hidden">

    <div class="panel-header">
        <h2>File Leave Request</h2>
    </div>

    <!-- Submits via POST to add_leave_process.php -->
    <form action="add_leave_process.php" method="POST" id="leaveForm">

        <!-- ==========================
             EMPLOYEE
        ========================== -->
        <div class="input-group">
            <label for="employee">Employee</label>

            <select id="employee" name="employee_id" class="inputs" required>

                <!-- Disabled placeholder option so nothing gets submitted
                     unless the user actually picks a real employee -->
                <option value="" disabled selected>Select employee</option>

                <?php
                // Only loop if the query succeeded and returned at least one row
                if (isset($result) && $result && mysqli_num_rows($result) > 0):
                    while ($row = mysqli_fetch_assoc($result)):
                ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>">
                        <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?>
                    </option>
                <?php
                    endwhile;
                else:
                ?>
                    <!-- Fallback shown if the employees table is empty or the query failed -->
                    <option value="" disabled>No employees available</option>
                <?php endif; ?>

            </select>
        </div>

        <!-- ==========================
             LEAVE TYPE
             Fixed dropdown of leave categories — no "Other" option,
             so any leave type not listed here can't be filed
        ========================== -->
        <div class="input-group">
            <label for="leaveType">Leave Type</label>

            <select id="leaveType" name="leave_type" class="inputs" required>
                <option value="" disabled selected>Select leave type</option>
                <option value="Vacation Leave">Vacation Leave</option>
                <option value="Sick Leave">Sick Leave</option>
                <option value="Emergency Leave">Emergency Leave</option>
                <option value="Maternity/Paternity Leave">Maternity/Paternity Leave</option>
                <option value="Bereavement Leave">Bereavement Leave</option>
                <option value="Unpaid Leave">Unpaid Leave</option>
            </select>
        </div>

        <!-- ==========================
             START DATE
        ========================== -->
        <div class="input-group">
            <label for="startDate">Start Date</label>
            <input type="date" id="startDate" name="start_date" class="inputs" required>
        </div>

        <!-- ==========================
             END DATE
        ========================== -->
        <div class="input-group">
            <label for="endDate">End Date</label>
            <input type="date" id="endDate" name="end_date" class="inputs" required>
        </div>

        <!-- ==========================
             TOTAL DAYS
             readonly — meant to be auto-calculated by JS from
             (End Date - Start Date), not typed in manually.
             NOTE: no JS for this calculation was included in the
             script.js file shared earlier, so this field currently
             won't populate itself unless that logic exists elsewhere.
        ========================== -->
        <div class="input-group">
            <label for="totalDays">Total Days</label>
            <input type="number" id="totalDays" name="total_days" class="inputs" placeholder="Automatically calculated" min="1" step="1" readonly required>
        </div>

        <!-- ==========================
             REASON
        ========================== -->
        <div class="input-group">
            <label for="reason">Reason</label>
            <textarea id="reason" name="reason" class="inputs" rows="4" placeholder="Briefly explain the reason for this leave request" required></textarea>
        </div>

        <!-- ==========================
             STATUS
             New leave requests are always Pending. Employee should
             not be able to choose Approved or Rejected — hardcoded
             as a hidden field so it can't be tampered with via the
             visible UI (though it can still be edited via browser
             devtools or a raw POST request, so add_leave_process.php
             should not blindly trust this value if approval logic
             matters server-side).
        ========================== -->
        <input type="hidden" name="status" value="Pending">

        <!-- ==========================
             FORM ACTIONS
        ========================== -->
        <div class="form-actions">
            <button type="submit" class="primary-btn">Save</button>
            <!-- type="button" — doesn't submit the form, needs JS to hide the panel -->
            <button type="button" class="primary-btn" id="cancelLeaveBtn">Cancel</button>
        </div>
    </form>
</div>