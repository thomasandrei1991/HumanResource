<div id="addEmployeeFormPanel" class="employee-form-panel hidden">
    <div class="panel-header">
        <h2 id="employeeFormTitle">Add New Employee</h2>
    </div>
    <form action="add_employee_process.php" method="POST" id="addEmployeeForm">
        <input type="hidden" id="employeeIdToEdit" name="edit_id" value="">
        <div class="input-group">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstname" class="inputs" placeholder="Enter first name" required>
        </div>
        <div class="input-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastname" class="inputs" placeholder="Enter last name" required>
        </div>
        <div class="input-group">
            <label for="employeeId">Employee ID</label>
            <input type="text" id="employeeId" name="employee_id" class="inputs" placeholder="E12345" required>
        </div>
        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="inputs" placeholder="email@example.com" required>
        </div>
        <div class="input-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="inputs" placeholder="(555) 123-4567" required>
        </div>
        <div class="input-group">
            <label for="department">Department</label>
            <input type="text" id="department" name="department" class="inputs" placeholder="Department name" required>
        </div>
        <div class="input-group">
            <label for="position">Position</label>
            <input type="text" id="position" name="position" class="inputs" placeholder="Job title" required>
        </div>
        <div class="input-group">
            <label for="dateHired">Date Hired</label>
            <input type="date" id="dateHired" name="date_hired" class="inputs" required>
        </div>
        <div class="input-group">
            <label for="salary">Salary</label>
            <input type="number" id="salary" name="salary" class="inputs" placeholder="0.00" step="0.01" required>
        </div>
        <div class="input-group">
            <label for="status">Status</label>
            <select id="status" name="employment_status" class="inputs" required>
                <option value="Active">Active</option>
                <option value="On Leave">On Leave</option>
                <option value="Pending">Pending</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="primary-btn" id="saveEmployeeBtn">Save</button>
            <button type="button" class="primary-btn" id="cancelAddEmployeeBtn">Cancel</button>
        </div>
    </form>
</div>    