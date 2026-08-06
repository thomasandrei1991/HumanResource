<?php
    include 'database.php';
    $sql = "SELECT id, firstname, lastname FROM employees ORDER BY firstname ASC";
    $result = mysqli_query($conn, $sql);
?>

<div class="employee-form-panel">
    <div class="panel-header">
        <h2>Add Attendance</h2>
    </div>
    <form action="add_attendance_process.php" method="POST">
        <div class="input-group">
            <label for="employee">Employee</label>
            <select id="employee" name="employee_id" class="inputs" required>
                <option value="">Select employee</option>
                <?php if (mysqli_num_rows($result) > 0) { while ($row = mysqli_fetch_assoc($result)) { ?>
                    <option value="<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?>
                    </option>
                <?php } } ?>
            </select>
        </div>

        <div class="input-group">
            <label for="attendanceDate">Date</label>
            <input type="date" id="attendanceDate" name="attendance_date" class="inputs" value="2026-08-06" required>
        </div>

        <div class="input-group">
            <label for="timeIn">Time In</label>
            <input type="text" id="timeIn" name="time_in" class="inputs" value="" placeholder="08:00">
        </div>

        <div class="input-group">
            <label for="timeOut">Time Out</label>
            <input type="text" id="timeOut" name="time_out" class="inputs" value="" placeholder="05:00">
        </div>

        <div class="input-group">
            <label for="attendanceStatus">Status</label>
            <select id="attendanceStatus" name="status" class="inputs" required>
                <option value="Present" selected>Present</option>
                <option value="Late">Late</option>
                <option value="Absent">Absent</option>
                <option value="On Leave">On Leave</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="primary-btn">Save</button>
        </div>
    </form>
</div>
