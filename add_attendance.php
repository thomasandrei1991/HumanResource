<?php
    // Load the database connection ($conn) from database.php
    include 'database.php';

    // Query to get every employee's id, firstname, and lastname,
    // sorted alphabetically by firstname
    $sql = "SELECT id, firstname, lastname FROM employees ORDER BY firstname ASC";
    // Run the query and store the result set (a mysqli_result object) in $result
    $result = mysqli_query($conn, $sql);
?>

<div class="employee-form-panel">
    <div class="panel-header">
        <h2>Add Attendance</h2>
    </div>

    <!-- This form submits via POST to add_attendance_process.php, which is the file you shared earlier -->
    <form action="add_attendance_process.php" method="POST">
        <div class="input-group">
            <label for="employee">Employee</label>
            <select id="employee" name="employee_id" class="inputs" required>
                <option value="">Select employee</option>
                <?php
                    // Only loop through results if there's at least one employee row
                    if (mysqli_num_rows($result) > 0) {
                        // Loop through each row of the result set one at a time
                        // mysqli_fetch_assoc returns each row as an associative array (e.g. $row['id'])
                        // and returns false/null when there are no more rows, ending the loop
                        while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <option value="<?php echo $row['id']; ?>">
                        <?php
                            // htmlspecialchars() escapes special characters (like <, >, &, quotes)
                            // to prevent XSS if a name ever contains HTML/script-like characters
                            echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
                        ?>
                    </option>
                <?php
                        } // end while
                    } // end if
                ?>
            </select>
        </div>

        <div class="input-group">
            <div class="input-group">
            <label for="attendanceDate">Date</label>
            <!-- Native HTML5 date picker input; hardcoded default value here, see note below -->
            <input type="date" id="attendanceDate" name="attendance_date" class="inputs" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="input-group">
            <label for="timeIn">Time In</label>
            <!-- Plain text input (not type="time"), so user can type freely; placeholder shows expected format -->
            <input type="text" id="timeIn" name="time_in" class="inputs" value="" placeholder="08:00">
        </div>

        <div class="input-group">
            <label for="timeOut">Time Out</label>
            <input type="text" id="timeOut" name="time_out" class="inputs" value="" placeholder="05:00">
        </div>

        <div class="input-group">
            <label for="attendanceStatus">Status</label>
            <!-- Dropdown of fixed status options; "Present" is selected by default -->
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