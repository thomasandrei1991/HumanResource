
<?php

    /*
    |--------------------------------------------------------------------------
    | DATABASE CONNECTION
    |--------------------------------------------------------------------------
    */

    include "database.php";


    /*
    |--------------------------------------------------------------------------
    | ROLE CHECK
    |--------------------------------------------------------------------------
    */

    $role = $_SESSION['role'] ?? '';

    if ($role !== 'Admin' && $role !== 'HR') {
        http_response_code(403);
        die("Access denied.");
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK ATTENDANCE DATA
    |--------------------------------------------------------------------------
    */

    if (!isset($attendance) || empty($attendance)) {
        die("Attendance record not found.");
    }

    /*
    |--------------------------------------------------------------------------
    | GET EMPLOYEES
    |--------------------------------------------------------------------------
    |
    | This is used by the Employee dropdown below.
    |
    */

    $employeesSql = "SELECT 
            id,
            firstname,
            lastname
        FROM employees
        ORDER BY firstname ASC, lastname ASC
    ";

    $employees = mysqli_query($conn, $employeesSql);

    if (!$employees) {
        die("Employee query failed: " .mysqli_error($conn));
    }

?>

<form action="edit_attendance_process.php" method="POST">

    <input type="hidden" name="id" value="<?php echo intval($attendance['id']); ?>">

    <!-- EMPLOYEE -->

    <div class="input-group">
        <label for="employee_id">Employee</label>
        <select id="employee_id" name="employee_id" class="inputs" required>

            <?php while ($emp = mysqli_fetch_assoc($employees)) { ?>
                <option value="<?php echo intval($emp['id']); ?>"
                    <?php
                        if (intval($emp['id']) === intval($attendance['employee_id'])) {
                            echo "selected";
                        }

                    ?>
                >
                    <?php echo htmlspecialchars($emp['firstname'] ." " .$emp['lastname']);?>
                </option>

            <?php } ?>
        </select>
    </div>


    <!-- DATE -->

    <div class="input-group">
        <label for="attendance_date">Date</label>
        <input type="date" id="attendance_date" name="attendance_date" class="inputs" 
            value="<?php echo htmlspecialchars($attendance['attendance_date']); ?>"
            required
        >
    </div>


    <!-- TIME IN -->

    <div class="input-group">
        <label for="time_in">Time In</label>
        <input type="time" id="time_in" name="time_in" class="inputs"
            value="<?php echo htmlspecialchars($attendance['time_in'] ?? ''); ?>"
        >
    </div>


    <!-- TIME OUT -->

    <div class="input-group">
        <label for="time_out">Time Out</label>
        <input type="time" id="time_out" name="time_out" class="inputs"
            value="<?php echo htmlspecialchars($attendance['time_out'] ?? ''); ?>"
        >
    </div>


    <!-- STATUS -->

    <div class="input-group">
        <label for="status">Status</label>
        <select id="status" name="status" class="inputs" required>
            <option value="Present"
                <?php
                    if ($attendance['status'] === 'Present') {
                        echo 'selected';
                    }
                ?>
            >
                Present
            </option>

            <option
                value="Late"
                <?php
                    if ($attendance['status'] === 'Late') {
                        echo 'selected';
                    }
                ?>
            >
                Late
            </option>


            <option
                value="Absent"
                <?php
                    if ($attendance['status'] === 'Absent') {
                        echo 'selected';
                    }
                ?>
            >
                Absent
            </option>


            <option
                value="On Leave"
                <?php
                    if ($attendance['status'] === 'On Leave') {
                        echo 'selected';
                    }
                ?>
            >
                On Leave
            </option>

        </select>

    </div>


    <!-- ACTIONS -->

    <div class="form-actions">
        <button type="submit" class="primary-btn">Update</button>
        <button type="button" class="primary-btn" onclick="window.location.href='attendance.php'">
            Cancel
        </button>
    </div>
</form>

