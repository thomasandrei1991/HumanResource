<?php
// Display errors para sa debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'database.php';

// ==========================================================
// 1. LOGIN CHECK
// ==========================================================
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ==========================================================
// 2. CURRENT USER & ROLE CHECK
// ==========================================================
$userId   = intval($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? '';

// Payagan ang 'Employee' at 'Admin' (Case-Insensitive Check)
if (strtolower($userRole) !== 'employee' && strtolower($userRole) !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// ==========================================================
// 3. DETERMINE TARGET EMPLOYEE ID
// ==========================================================
$employeeId = 0;

if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $employeeId = intval($_GET['id']);
} elseif (isset($_SESSION['employee_id']) && intval($_SESSION['employee_id']) > 0) {
    $employeeId = intval($_SESSION['employee_id']);
}

// Kapag Admin at walang binigay na ID sa URL o Session
// Palitan ang: if ($employeeId === 0) { die(...); }
// Ng ganitong code:

if ($employeeId === 0) {
    // Kung Admin at walang ID, ibalik sa employee list
    header("Location: employee.php"); 
    exit();
}

// ==========================================================
// 4. PROCESS EDIT PROFILE SUBMISSION
// ==========================================================
$successMsg = $_SESSION['successMsg'] ?? '';
$errorMsg   = $_SESSION['errorMsg'] ?? '';

// Linisin ang session message pagkatapos kunin para hindi paulit-ulit lumabas
unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');

    if (empty($firstname) || empty($lastname) || empty($email)) {
        $_SESSION['errorMsg'] = "First Name, Last Name, and Email are required fields.";
    } else {
        $updateStmt = mysqli_prepare(
            $conn,
            "UPDATE employees 
             SET firstname = ?, lastname = ?, email = ?, phone = ? 
             WHERE id = ?"
        );

        if ($updateStmt) {
            mysqli_stmt_bind_param(
                $updateStmt,
                "ssssi",
                $firstname,
                $lastname,
                $email,
                $phone,
                $employeeId
            );

            if (mysqli_stmt_execute($updateStmt)) {
                $_SESSION['successMsg'] = "Profile updated successfully!";
            } else {
                $_SESSION['errorMsg'] = "Failed to update profile: " . mysqli_error($conn);
            }

            mysqli_stmt_close($updateStmt);
        } else {
            $_SESSION['errorMsg'] = "Database error: Unable to prepare update query.";
        }
    }

    // Isama pa rin ang ?id= sa redirect URL
    header("Location: employee_profile.php?id=" . $employeeId);
    exit();
}

// ==========================================================
// 5. GET LOGGED-IN / TARGET EMPLOYEE DATA
// ==========================================================
$employee = null;

if ($employeeId > 0) {
    $employeeQuery = mysqli_prepare(
        $conn,
        "SELECT
            id,
            employee_id,
            firstname,
            lastname,
            email,
            phone,
            department,
            position,
            date_hired,
            salary,
            employment_status
        FROM employees
        WHERE id = ?
        LIMIT 1"
    );

    if ($employeeQuery) {
        mysqli_stmt_bind_param($employeeQuery, "i", $employeeId);
        mysqli_stmt_execute($employeeQuery);

        $employeeResult = mysqli_stmt_get_result($employeeQuery);

        if ($employeeResult && mysqli_num_rows($employeeResult) > 0) {
            $employee = mysqli_fetch_assoc($employeeResult);
        }

        mysqli_stmt_close($employeeQuery);
    }
}

// ==========================================================
// 6. EMPLOYEE NOT FOUND CHECK
// ==========================================================
if (!$employee) {
    die("Employee profile could not be found.");
}

// ==========================================================
// 7. EMPLOYEE DISPLAY INFORMATION
// ==========================================================
$fullName = $employee['firstname'] . ' ' . $employee['lastname'];

$initials = strtoupper(
    substr($employee['firstname'], 0, 1) .
    substr($employee['lastname'], 0, 1)
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <!-- GLOBAL STYLES -->
    <link
        rel="stylesheet"
        href="styles/common.css"
    >

    <!-- PROFILE STYLES -->
    <link
        rel="stylesheet"
        href="styles/employee_profile.css"
    >

    <title>My Profile | HR Dashboard</title>

</head>


<body class="dashboard-page">

<div class="dashboard-shell">

    <?php include 'sidebar.php'; ?>


    <!-- ======================================================
         MAIN CONTENT
    ======================================================= -->

    <main class="dashboard-main">

        <div class="employee-container">


            <!-- ==================================================
                 PAGE HEADER
            =================================================== -->

            <div class="page-header">

                <div>

                    <p class="page-kicker">
                        My Account
                    </p>

                    <h1>
                        Profile
                    </h1>

                </div>

            </div>


            <!-- NOTIFICATION BANNERS -->
            <?php if (!empty($successMsg)): ?>
                <div class="alert-banner success">
                    <?php echo htmlspecialchars($successMsg); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert-banner error">
                    <?php echo htmlspecialchars($errorMsg); ?>
                </div>
            <?php endif; ?>

            <!-- Ilagay ito sa employee_profile.php (sa itaas ng .page-header o loob ng container) -->
            <div class="back-navigation" style="margin-bottom: 20px;">
                <a href="employee.php" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 8px 16px; border-radius: 6px;">
                    &larr; Back to Employees
                </a>
            </div>
            
            <!-- ==================================================
                 PROFILE HEADER
            =================================================== -->

            <section class="profile-header-card">

                <div class="profile-header-content">


                    <!-- PROFILE PHOTO -->

                    <div class="profile-photo-wrapper">

                        <div class="profile-photo">

                            <span>
                                <?php echo htmlspecialchars($initials); ?>
                            </span>

                        </div>

                    </div>


                    <!-- PROFILE BASIC INFORMATION -->

                    <div class="profile-basic-info">

                        <h2>
                            <?php
                                echo htmlspecialchars($fullName);
                            ?>
                        </h2>

                        <p class="profile-position">

                            <?php
                                echo htmlspecialchars(
                                    $employee['position']
                                );
                            ?>

                        </p>

                        <p class="profile-department">

                            <?php
                                echo htmlspecialchars(
                                    $employee['department']
                                );
                            ?>

                        </p>


                        <!-- STATUS -->

                        <div class="profile-status">

                            <span class="status-dot"></span>

                            <?php
                                echo htmlspecialchars(
                                    $employee['employment_status']
                                );
                            ?>

                        </div>

                    </div>


                    <!-- EDIT BUTTON -->

                    <div class="profile-header-action">

                        <button
                            type="button"
                            class="primary-btn"
                            id="openEditModalBtn"
                        >
                            Edit Profile
                        </button>

                    </div>

                </div>

            </section>


            <!-- ==================================================
                 PERSONAL INFORMATION
            =================================================== -->

            <section class="profile-panel">

                <div class="profile-panel-header">

                    <div>

                        <p class="profile-section-kicker">
                            Personal Information
                        </p>

                        <h2>
                            Basic Details
                        </h2>

                    </div>

                </div>


                <div class="profile-info-grid">


                    <!-- FIRST NAME -->

                    <div class="profile-info-item">

                        <span class="profile-info-label">
                            First Name
                        </span>

                        <span class="profile-info-value">

                            <?php
                                echo htmlspecialchars(
                                    $employee['firstname']
                                );
                            ?>

                        </span>

                    </div>


                    <!-- LAST NAME -->

                    <div class="profile-info-item">

                        <span class="profile-info-label">
                            Last Name
                        </span>

                        <span class="profile-info-value">

                            <?php
                                echo htmlspecialchars(
                                    $employee['lastname']
                                );
                            ?>

                        </span>

                    </div>


                    <!-- EMAIL -->

                    <div class="profile-info-item">

                        <span class="profile-info-label">
                            Email Address
                        </span>

                        <span class="profile-info-value">

                            <?php
                                echo htmlspecialchars(
                                    $employee['email'] ?? 'Not provided'
                                );
                            ?>

                        </span>

                    </div>


                    <!-- PHONE -->

                    <div class="profile-info-item">

                        <span class="profile-info-label">
                            Phone Number
                        </span>

                        <span class="profile-info-value">

                            <?php
                                echo htmlspecialchars(
                                    $employee['phone'] ?? 'Not provided'
                                );
                            ?>

                        </span>

                    </div>

                </div>

            </section>


            <!-- ==================================================
                 EMPLOYMENT INFORMATION
            =================================================== -->

            <section class="profile-panel">

                <div class="profile-panel-header">

                    <div>

                        <p class="profile-section-kicker">
                            Employment
                        </p>

                        <h2>
                            Employment Information
                        </h2>

                    </div>

                </div>


                <div class="profile-info-grid">


                    <!-- EMPLOYEE ID -->

                    <div class="profile-info-item">

                        <span class="profile-info-label">
                            Employee ID
                        </span>

                        <span class="profile-info-value">

                            <?php
                                echo htmlspecialchars(
                                    $employee['employee_id']
                                );
                            ?>

                        </span>

                    </div>


                    <!-- DEPARTMENT -->

                    <div class="profile-info-item">

                        <span class="profile-info-label">
                            Department
                        </span>

                        <span class="profile-info-value">

                            <?php
                                echo htmlspecialchars(
                                    $employee['department']
                                );
                            ?>

                        </span>

                    </div>


                    <!-- POSITION -->

                    <div class="profile-info-item">

                        <span class="profile-info-label">
                            Position
                        </span>

                        <span class="profile-info-value">

                            <?php
                                echo htmlspecialchars(
                                    $employee['position']
                                );
                            ?>

                        </span>

                    </div>


                    <!-- DATE HIRED -->

                    <div class="profile-info-item">

                        <span class="profile-info-label">
                            Date Hired
                        </span>

                        <span class="profile-info-value">

                            <?php

                            if (!empty($employee['date_hired'])) {

                                echo date(
                                    'F d, Y',
                                    strtotime(
                                        $employee['date_hired']
                                    )
                                );

                            } else {

                                echo 'Not provided';

                            }

                            ?>

                        </span>

                    </div>


                    <!-- EMPLOYMENT STATUS -->

                    <div class="profile-info-item">

                        <span class="profile-info-label">
                            Employment Status
                        </span>

                        <span class="profile-info-value">

                            <span class="profile-status-badge">

                                <?php
                                    echo htmlspecialchars(
                                        $employee['employment_status']
                                    );
                                ?>

                            </span>

                        </span>

                    </div>


                </div>

            </section>


        </div>

    </main>

</div>


<!-- ==========================================================
     EDIT PROFILE MODAL
=========================================================== -->

<div class="modal-overlay" id="editProfileModal">

    <div class="modal-content">

        <div class="modal-header">

            <h3>Edit Profile Details</h3>

        </div>

        <form action="employee_profile.php?id=<?php echo $employeeId; ?>" method="POST">

            <input type="hidden" name="action" value="update_profile">

            <div class="modal-body">

                <div class="form-row">

                    <div class="form-group">
                        <label for="firstname">First Name *</label>
                        <input
                            type="text"
                            id="firstname"
                            name="firstname"
                            class="form-control"
                            value="<?php echo htmlspecialchars($employee['firstname']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="lastname">Last Name *</label>
                        <input
                            type="text"
                            id="lastname"
                            name="lastname"
                            class="form-control"
                            value="<?php echo htmlspecialchars($employee['lastname']); ?>"
                            required
                        >
                    </div>

                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="<?php echo htmlspecialchars($employee['email']); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        class="form-control"
                        value="<?php echo htmlspecialchars($employee['phone'] ?? ''); ?>"
                        placeholder="e.g. 09123456789"
                    >
                </div>

            </div>

            <div class="modal-actions">
                <button type="submit" class="primary-btn">
                    Save Changes
                </button>

                <button type="button" class="btn-secondary" id="cancelEditModalBtn">
                    Cancel
                </button>

            
            </div>

        </form>

    </div>

</div>


<!-- ==========================================================
     SCRIPT FOR MODAL TOGGLE
=========================================================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ==========================================
    // 1. EDIT PROFILE MODAL HANDLERS
    // ==========================================
    const editModal = document.getElementById('editProfileModal');
    const openBtn   = document.getElementById('openEditModalBtn');
    const closeBtn  = document.getElementById('closeEditModalBtn');
    const cancelBtn = document.getElementById('cancelEditModalBtn');

    function openModal() {
        if (editModal) {
            editModal.classList.add('active');
            editModal.style.display = 'flex';
        }
    }

    function closeModal() {
        if (editModal) {
            editModal.classList.remove('active');
            editModal.style.display = 'none';
        }
    }

    if (openBtn) {
        openBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openModal();
        });
    }
    
    if (closeBtn)  closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    if (editModal) {
        editModal.addEventListener('click', function (e) {
            if (e.target === editModal) {
                closeModal();
            }
        });
    }

    // ==========================================
    // 2. CLICKABLE TABLE ROWS HANDLER
    // ==========================================
    const rows = document.querySelectorAll('.clickable-row');
    
    rows.forEach(row => {
        row.addEventListener('click', function (e) {
            // Huwag mag-redirect kung Pindutan, Link, o Input sa loob ng row ang kini-click
            if (!e.target.closest('a, button, input')) {
                const targetUrl = this.dataset.href;
                if (targetUrl) {
                    window.location.href = targetUrl;
                }
            }
        });
    });
});
</script>


</body>

</html>