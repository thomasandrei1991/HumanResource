<?php

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once 'database.php';


    // ==========================
    // LOGIN CHECK
    // ==========================

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }


    // ==========================
    // CURRENT USER
    // ==========================

    $userId = intval($_SESSION['user_id']);
    $userRole = $_SESSION['role'] ?? '';


    // ==========================
    // ROLE CHECK
    // ==========================

    $isAdminOrHR = ($userRole === 'Admin' || $userRole === 'HR');

    if (!$isAdminOrHR) {
        header("Location: dashboard.php");
        exit();
    }


    // ==========================
    // ACTIVE DEPARTMENTS
    // ==========================

    $departmentQuery = mysqli_query(
        $conn,
        "SELECT department_name
        FROM departments
        WHERE status = 'Active'
        ORDER BY department_name ASC"
    );


    // ==========================
    // EMPLOYEE SUMMARY
    // ==========================

    $totalEmployees = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total FROM employees"
        )
    )['total'];

    $activeEmployees = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total
            FROM employees
            WHERE employment_status = 'Active'"
        )
    )['total'];

    $onLeaveEmployees = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total
            FROM employees
            WHERE employment_status = 'On Leave'"
        )
    )['total'];

?>



<!-- 
    This panel is hidden by default ("hidden" class) — it's meant to be shown via JS 
    when the user clicks an "Add Employee" or "Edit Employee" button elsewhere on the page 
-->
<div id="addEmployeeFormPanel" class="employee-form-panel hidden">
    <div class="panel-header">
        <!-- Title text is likely swapped via JS between "Add New Employee" and "Edit Employee" -->
        <h2 id="employeeFormTitle">Add New Employee</h2>
    </div>

    <!-- Form submits via POST to add_employee_process.php (the file from your previous message) -->
    <form action="add_employee_process.php" method="POST" id="addEmployeeForm">

        <!-- 
            Hidden field to hold an employee's ID when editing an existing record.
            Left blank for "Add" mode; JS presumably fills this in when editing,
            so add_employee_process.php can tell an insert apart from an update.
            Note: add_employee_process.php currently never reads 'edit_id', so this
            field isn't doing anything yet on the backend.
        -->
        <input type="hidden" id="employeeIdToEdit" name="edit_id" value="">

        <div class="input-group">
            <label for="firstName">First Name</label>
            <!-- required = browser won't let the form submit if this is empty -->
            <input type="text" id="firstName" name="firstname" class="inputs" placeholder="Enter first name" required>
        </div>

        <div class="input-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastname" class="inputs" placeholder="Enter last name" required>
        </div>

        <div class="input-group">
            <label for="employeeId">Employee ID</label>
            <!-- Plain text field, placeholder hints at expected format (e.g. E12345) but nothing enforces it -->
            <input type="text" id="employeeId" name="employee_id" class="inputs" placeholder="E12345" required>
        </div>

        <div class="input-group">
            <label for="email">Email</label>
            <!-- type="email" gives basic built-in browser validation (must contain @, etc.) -->
            <input type="email" id="email" name="email" class="inputs" placeholder="email@example.com" required>
        </div>

        <div class="input-group">
            <label for="phone">Phone Number</label>
            <!-- type="tel" doesn't enforce a specific format by itself, it's mostly a hint for mobile keyboards -->
            <input type="tel" id="phone" name="phone" class="inputs" placeholder="(555) 123-4567" required>
        </div>

        <div class="input-group">
            <label for="department">Department</label>

            <select
                id="department"
                name="department"
                class="inputs"
                required
            >
                <option value="" disabled selected>
                    Select department
                </option>

                <?php
                if ($departmentQuery && mysqli_num_rows($departmentQuery) > 0):

                    while ($department = mysqli_fetch_assoc($departmentQuery)):
                ?>

                    <option value="<?php echo htmlspecialchars($department['department_name']); ?>">
                        <?php echo htmlspecialchars($department['department_name']); ?>
                    </option>

                <?php
                    endwhile;

                else:
                ?>

                    <option value="" disabled>
                        No departments available
                    </option>

                <?php endif; ?>

            </select>
        </div>

        <div class="input-group">
            <label for="position">Position</label>
            <input type="text" id="position" name="position" class="inputs" placeholder="Job title" required>
        </div>

        <div class="input-group">
            <label for="dateHired">Date Hired</label>
            <!-- Native date picker, no default value here (unlike the attendance form) -->
            <input type="date" id="dateHired" name="date_hired" class="inputs" required>
        </div>

        <div class="input-group">
            <label for="salary">Salary</label>
            <!-- type="number" with step="0.01" allows decimal values like 25000.50 -->
            <input type="number" id="salary" name="salary" class="inputs" placeholder="0.00" step="0.01" required>
        </div>

        <div class="input-group">
            <label for="status">Status</label>
            <!-- Fixed set of employment status options -->
            <select id="status" name="employment_status" class="inputs" required>
                <option value="Active">Active</option>
                <option value="On Leave">On Leave</option>
                <option value="Pending">Pending</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        <div class="form-actions">
            <!-- type="submit" triggers normal form submission -->
            <button type="submit" class="primary-btn" id="saveEmployeeBtn">Save</button>
            <!-- type="button" does NOT submit the form; needs JS to handle closing/hiding the panel -->
            <button type="button" class="primary-btn" id="cancelAddEmployeeBtn">Cancel</button>
        </div>
    </form>
</div>