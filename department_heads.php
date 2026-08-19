<?php

    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $currentPage = basename($_SERVER['PHP_SELF']);
    $userRole = $_SESSION['role'] ?? '';

    require_once 'database.php';

    // ========================================
    // ADMIN ACCESS ONLY
    // ========================================

    if ($userRole !== 'Admin') {
        header("Location: dashboard.php");
        exit();
    }

    // ========================================
    // GET DEPARTMENT HEADS
    // ========================================

    $departmentHeadsQuery = mysqli_query(
        $conn,
        "SELECT 
            u.id AS user_id,
            u.fullname,
            u.username,
            u.role,
            d.department_name,
            d.status AS department_status
        FROM users u
        LEFT JOIN departments d
            ON d.department_head = u.fullname
        WHERE u.role = 'Department Head'
        ORDER BY u.fullname ASC"
    );

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/department_heads.css">
    <title>Department Heads | HR Dashboard</title>
</head>

<body class="dashboard-page">
<div class="dashboard-shell">
    <?php include 'sidebar.php'; ?>
    <main class="dashboard-main">
        <div class="dashboard-container">
            <div class="department-container">

                <!-- ==========================
                    PAGE HEADER
                ========================== -->

                <div class="page-header">
                    <div>
                        <p class="page-kicker">Organization Management</p>
                        <h1>Department Heads</h1>
                    </div>
                    <button onclick="window.location.href='add_department_head.php'" class="primary-btn">
                        + Add Department Head
                    </button>
                </div>


                <!-- ==========================
                    DEPARTMENT HEAD TABLE
                ========================== -->

                <div class="employee-panel">
                    <div class="panel-header">
                        
                    </div>

                    <table class="dashboard-table employee-table">
                        <thead>
                            <tr>
                                <th>Department Head</th>
                                <th>Department</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php

                            if ($departmentHeadsQuery && mysqli_num_rows($departmentHeadsQuery) > 0):
                                while ($head = mysqli_fetch_assoc($departmentHeadsQuery)):
                                    $fullname = $head['fullname'];
                                    $departmentName = $head['department_name']?? 'Not Assigned';
                                    $username = $head['username'];
                                    $departmentStatus = $head['department_status']?? 'Inactive';

                                    // ========================================
                                    // INITIALS
                                    // ========================================

                                    $words = explode(' ', $fullname);
                                    $initials = '';

                                    foreach ($words as $word) {
                                        if (!empty($word)) {
                                            $initials .= strtoupper(substr($word, 0, 1));
                                        }
                                    }

                                    $initials = substr($initials, 0, 2);

                                    // ========================================
                                    // STATUS CLASS
                                    // ========================================

                                    if ($departmentStatus === 'Active') {
                                        $statusClass = 'present';
                                    } 
                                    else {
                                        $statusClass = 'absent';
                                    }

                        ?>

                            <tr>

                                <!-- ==========================
                                    NAME
                                ========================== -->
                                <td>
                                    <div class="employee-name">
                                        <div class="emp-avatar blue-bg">
                                            <?php echo htmlspecialchars($initials);?>
                                        </div>
                                        <?php echo htmlspecialchars($fullname);?>
                                    </div>
                                </td>


                                <!-- ==========================
                                    DEPARTMENT
                                ========================== -->

                                <td><?php echo htmlspecialchars($departmentName);?></td>

                                <!-- ==========================
                                    USERNAME
                                ========================== -->

                                <td><?php echo htmlspecialchars($username);?></td>

                                <!-- ==========================
                                    STATUS
                                ========================== -->

                                <td>
                                    <span class="status-badge
                                        <?php echo $statusClass;?>">
                                        <?php echo htmlspecialchars($departmentStatus);?>
                                    </span>
                                </td>


                                <!-- ==========================
                                    ACTION
                                ========================== -->

                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="edit-btn" 
                                            onclick="window.location.href='edit_department_head.php?id=<?php echo $head['user_id']; ?>'"
                                        >
                                            Edit
                                        </button>

                                        <button type="button" class="employee-delete-btn delete-btn"
                                            data-id="<?php echo $head['user_id']; ?>"
                                            data-type="department_head"
                                            data-name="<?php echo htmlspecialchars($fullname); ?>"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php
                            endwhile;
                            else:
                        ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">
                                    No department heads found.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ==========================
    DELETE MODAL
========================== -->

<div id="deleteModal" class="modal hidden" role="dialog" aria-modal="true">
    <div class="modal-content">
        <h3>Delete Department Head</h3>
        <p id="deleteMessage">Are you sure you want to delete this department head?</p>
        <div class="form-actions">
            <button type="button" class="primary-btn" id="confirmDeleteBtn">Delete</button>
            <button type="button" class="primary-btn" id="cancelDeleteBtn">Cancel</button>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>