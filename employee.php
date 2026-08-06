<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/employee.css">
    <title>Employees | HR Dashboard</title>
</head>
<body class="dashboard-page"
      data-show-form="<?php echo isset($_SESSION['employee_error']) ? '1' : '0'; ?>"
      data-show-modal="<?php echo isset($_SESSION['employee_error']) ? '1' : '0'; ?>"
      data-modal-title="Duplicate Employee ID"
      data-modal-message="<?php echo isset($_SESSION['employee_error']) ? htmlspecialchars($_SESSION['employee_error'], ENT_QUOTES, 'UTF-8') : ''; ?>">
    <div class="dashboard-shell">
        <?php include 'sidebar.php'; ?>
        <main class="dashboard-main">
            <div class="dashboard-container">
                <div class="employee-container">
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">People Management</p>
                            <h1>Employees</h1>
                        </div>
                        <div class="table-tools">
                            <form class="employee-search-form" action="employee.php" method="GET">
                                <input class="search-input" type="text" name="search" placeholder="Search employee..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                <button class="search-btn" type="submit">Search</button>
                            </form>
                        </div>
                        <button class="primary-btn" id="addEmployeeBtn">+ Add Employee</button>
                    </div>
                    <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Total Staff</h3>
                            <p>248</p>
                        </div>
                        <div class="summary-card green">
                            <h3>Active</h3>
                            <p>231</p>
                        </div>
                        <div class="summary-card purple">
                            <h3>On Leave</h3>
                            <p>17</p>
                        </div>
                    </div>
                    <?php
                        $error = $_GET['error'] ?? '';
                        $success = $_GET['success'] ?? '';
                        $isEditMode = isset($_GET['edit_id']) && !empty($_GET['edit_id']);
                        if ($isEditMode) {
                            include 'edit_employee.php';
                        } else {
                            include 'add_employee.php';
                        }
                    ?>
                    <div class="employee-panel">
                        <div class="panel-header">
                            <h2>Employee Directory</h2>
                        </div>
                        <table class="dashboard-table employee-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    include "database.php";
                                    // Search Function
                                    if(isset($_GET['search']) && !empty($_GET['search'])){
                                        $search = mysqli_real_escape_string($conn, $_GET['search']);
                                        $sql = "SELECT * FROM employees
                                                WHERE firstname LIKE '%$search%'
                                                OR lastname LIKE '%$search%'
                                                OR employee_id LIKE '%$search%'
                                                ORDER BY id DESC";
                                    }else{
                                        $sql = "SELECT * FROM employees ORDER BY id DESC";
                                    }
                                    $result = mysqli_query($conn, $sql);
                                    if(mysqli_num_rows($result) > 0){
                                        while($row = mysqli_fetch_assoc($result)){
                                            $initials = strtoupper(
                                                substr($row['firstname'], 0, 1) .
                                                substr($row['lastname'], 0, 1)
                                            );
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="employee-name">
                                                <div class="emp-avatar blue-bg">
                                                    <?php echo $initials; ?>
                                                </div>
                                                <?php echo $row['firstname'] . " " . $row['lastname']; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo $row['department']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['position']; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge present">
                                                <?php echo $row['employment_status']; ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <a href="employee.php?edit_id=<?php echo $row['id']; ?>" class="edit-btn" data-employee-id="<?php echo $row['id']; ?>">
                                                ✏️ Edit
                                            </a>
                                            <button type="button" class="employee-delete-btn"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-name="<?php echo $row['firstname'] . ' ' . $row['lastname']; ?>">
                                                🗑️ Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    }
                                    else{
                                    ?>
                                    <tr>
                                        <td colspan="4" style="text-align:center;">
                                            No employees found.
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div id="errorModal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-content">
            <h3 id="modalTitle">Employee Notice</h3>
            <p id="modalMessage">Please complete the form.</p>
            <button type="button" id="closeModalBtn">Close</button>
        </div>
    </div>
    <div id="deleteModal" class="modal hidden">
        <div class="modal-content">
            <h3>Delete Employee</h3>
            <p id="deleteMessage">
                Are you sure you want to delete this employee?
            </p>
            <div class="modal-buttons">
                <button id="cancelDeleteBtn" class="primary-btn">
                    Cancel
                </button>
                <button id="confirmDeleteBtn" class="delete-btn">
                    Delete
                </button>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
    <?php if (isset($_SESSION['employee_error'])): ?>
    <script>
        window.onload = function () {
            document.getElementById('modalTitle').textContent = 'Duplicate Employee ID';
            document.getElementById('modalMessage').textContent = <?php echo json_encode($_SESSION['employee_error']); ?>;
            document.getElementById('errorModal').classList.remove('hidden');
        };
    </script>
    <?php unset($_SESSION['employee_error']); endif; ?>
    <div id="employeeModal" class="modal hidden">
        <div class="modal-content">
            <h3 id="employeeModalTitle"></h3>
            <p id="employeeModalMessage"></p>
            <button id="closeEmployeeModal">
                OK
            </button>
        </div>
    </div>
</body>
</html>
