<div id="addDepartmentFormPanel" class="employee-form-panel hidden">

    <div class="panel-header">
        <h2>Add New Department</h2>
    </div>

    <form action="add_department_process.php" method="POST" id="addDepartmentForm">

        <div class="input-group">
            <label for="departmentName">Department Name</label>

            <input
                type="text"
                id="departmentName"
                name="department_name"
                class="inputs"
                placeholder="Enter department name"
                required
            >
        </div>


        <div class="input-group">
            <label for="departmentHead">Department Head</label>

            <input
                type="text"
                id="departmentHead"
                name="department_head"
                class="inputs"
                placeholder="Enter department head"
            >
        </div>


        <div class="input-group">
            <label for="departmentStatus">Status</label>

            <select
                id="departmentStatus"
                name="status"
                class="inputs"
                required
            >
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>


        <div class="form-actions">

            <button
                type="submit"
                class="primary-btn"
            >
                Save
            </button>

            <button
                type="button"
                class="primary-btn"
                id="cancelAddDepartmentBtn"
            >
                Cancel
            </button>

        </div>

    </form>

</div>