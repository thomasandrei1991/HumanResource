<?php

    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    require_once 'database.php';

    // ==========================
    // GET DEPARTMENT ID
    // ==========================

    $departmentId = intval($_GET['id'] ?? 0);

    if ($departmentId <= 0) {
        header("Location: departments.php?error=invalid_id");
        exit();
    }


    // ==========================
    // GET DEPARTMENT
    // ==========================

    $query = mysqli_prepare($conn,"SELECT id, department_name, department_head, status
        FROM departments
        WHERE id = ?"
    );

    mysqli_stmt_bind_param($query, "i", $departmentId);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $department = mysqli_fetch_assoc($result);


    // ==========================
    // CHECK IF FOUND
    // ==========================

    if (!$department) {
        header("Location: departments.php?error=not_found");
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <title>Edit Department</title>
</head>

<body class="dashboard-page">
<div class="dashboard-shell">
    <?php include 'sidebar.php'; ?>
    <main class="dashboard-main">
        <div class="dashboard-container">
            <div class="employee-form-panel">
                <div class="panel-header">
                    <h2>Edit Department</h2>
                </div>

                <form action="edit_department_process.php" method="POST">

                    <!-- Department ID -->
                    <input type="hidden" name="id" value="<?php echo $department['id']; ?>">
                    <!-- Department Name -->
                    <div class="input-group">
                        <label for="departmentName">Department Name</label>
                        <input type="text" id="departmentName" name="department_name" class="inputs" value="<?php echo htmlspecialchars($department['department_name']); ?>" required>
                    </div>

                    <!-- Department Head -->
                    <div class="input-group">
                        <label for="departmentHead">Department Head</label>
                        <input type="text" id="departmentHead" name="department_head" class="inputs" value="<?php echo htmlspecialchars($department['department_head']); ?>" placeholder="Enter department head">
                    </div>

                    <!-- Status -->
                    <div class="input-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="inputs" required>
                            <option value="Active" <?php echo $department['status'] === 'Active' ? 'selected' : ''; ?>>
                                Active
                            </option>
                            <option value="Inactive" <?php echo $department['status'] === 'Inactive'? 'selected' : '';?>>
                                Inactive
                            </option>
                        </select>

                    </div>

                    <!-- ACTIONS -->
                    <div class="form-actions">
                        <button type="submit" class="primary-btn">
                            Save Changes
                        </button>
                        <button type="button" class="primary-btn" onclick="window.location.href='departments.php'">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>