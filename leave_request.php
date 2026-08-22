<?php
// This file is meant to be included from leave_management.php,
// which already ran session_start(), validated the user, loaded
// database.php, and computed $employeeId for the logged-in Employee.
// We simply reuse those variables here.
?>

<!-- ==========================
     LEAVE REQUEST FORM
     Hidden by default ("hidden" class) — toggled visible via JS
     when the "+ New Leave" button is clicked
========================== -->
<div id="leaveFormPanel" class="leave-form-wrapper hidden">

    <div class="panel-header">
        <h2>File Leave Request</h2>
    </div>

    <!-- Submits via POST to add_leave_process.php -->
    <form action="add_leave_process.php" method="POST" id="leaveForm">

        <!-- ==========================
             EMPLOYEE
             Readonly — auto-filled from the logged-in session.
             Employees cannot file leave on behalf of someone else.
        ========================== -->
        <div class="input-group">
            <label for="employee">Employee</label>

            <input
                type="text"
                id="employee"
                class="inputs"
                value="<?php echo htmlspecialchars($_SESSION['fullname'] ?? ''); ?>"
                readonly
            >

            <input
                type="hidden"
                name="employee_id"
                value="<?php echo htmlspecialchars($employeeId ?? ''); ?>"
            >
        </div>

        <!-- ==========================
             LEAVE TYPE
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
             Auto-calculated by JS (already handled in leave_management.php's
             <script> block via calculateLeaveDays())
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

        <!-- Always Pending on creation -->
        <input type="hidden" name="status" value="Pending">

        <!-- ==========================
             FORM ACTIONS
        ========================== -->
        <div class="form-actions">
            <button type="submit" class="primary-btn">Save</button>
            <button type="button" class="primary-btn" id="cancelLeaveBtn">Cancel</button>
        </div>
    </form>
</div>