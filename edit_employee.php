<?php
include "database.php";

// Check kung may edit_id
if (!isset($_GET['edit_id'])) {
    header("Location: employee.php");
    exit();
}

$id = $_GET['edit_id'];

// Kunin ang employee data
$sql = "SELECT * FROM employees WHERE id='$id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Employee not found.");
}

$employee = mysqli_fetch_assoc($result);
?>

<div id="editEmployeeFormPanel" class="employee-form-panel">

    <div class="panel-header">
        <h2>Edit Employee</h2>
    </div>

    <form action="edit_employee_process.php" method="POST">

        <!-- Hidden ID -->
        <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">

        <div class="input-group">
            <label>First Name</label>
            <input
                type="text"
                name="firstname"
                class="inputs"
                value="<?php echo $employee['firstname']; ?>"
                required>
        </div>

        <div class="input-group">
            <label>Last Name</label>
            <input
                type="text"
                name="lastname"
                class="inputs"
                value="<?php echo $employee['lastname']; ?>"
                required>
        </div>

        <div class="input-group">
            <label>Employee ID</label>
            <input
                type="text"
                name="employee_id"
                class="inputs"
                value="<?php echo $employee['employee_id']; ?>"
                required>
        </div>

        <div class="input-group">
            <label>Email</label>
            <input
                type="email"
                name="email"
                class="inputs"
                value="<?php echo $employee['email']; ?>"
                required>
        </div>

        <div class="input-group">
            <label>Phone</label>
            <input
                type="text"
                name="phone"
                class="inputs"
                value="<?php echo $employee['phone']; ?>"
                required>
        </div>

        <div class="input-group">
            <label>Department</label>
            <input
                type="text"
                name="department"
                class="inputs"
                value="<?php echo $employee['department']; ?>"
                required>
        </div>

        <div class="input-group">
            <label>Position</label>
            <input
                type="text"
                name="position"
                class="inputs"
                value="<?php echo $employee['position']; ?>"
                required>
        </div>

        <div class="input-group">
            <label>Date Hired</label>
            <input
                type="date"
                name="date_hired"
                class="inputs"
                value="<?php echo $employee['date_hired']; ?>"
                required>
        </div>

        <div class="input-group">
            <label>Salary</label>
            <input
                type="number"
                name="salary"
                class="inputs"
                value="<?php echo $employee['salary']; ?>"
                required>
        </div>

        <div class="input-group">
            <label>Status</label>

            <select name="employment_status" class="inputs">

                <option value="Active"
                    <?php if($employee['employment_status']=="Active") echo "selected"; ?>>
                    Active
                </option>

                <option value="On Leave"
                    <?php if($employee['employment_status']=="On Leave") echo "selected"; ?>>
                    On Leave
                </option>

                <option value="Pending"
                    <?php if($employee['employment_status']=="Pending") echo "selected"; ?>>
                    Pending
                </option>

                <option value="Inactive"
                    <?php if($employee['employment_status']=="Inactive") echo "selected"; ?>>
                    Inactive
                </option>

            </select>

        </div>
        <div class="form-actions">
            <button type="submit" class="primary-btn">
                Update Employee
            </button>
            <button type="button" class="primary-btn" id="cancelEditEmployeeBtn">
                Cancel
            </button>
        </div>
    </form>
</div>