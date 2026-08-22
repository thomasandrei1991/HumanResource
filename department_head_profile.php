<?php
session_start();
require_once 'database.php';

// 1. Check user authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = intval($_SESSION['user_id']);
$successMsg = '';
$errorMsg = '';

/* ------------------------------------------------------------
   1. HANDLE PROFILE UPDATE (NAME, EMAIL, & PHONE)
   ------------------------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $newFullName = trim($firstname . ' ' . $lastname);

    if (!empty($newFullName)) {
        $hasError = false;

        // A. Update Name sa departments table
        $updateDeptQuery = "UPDATE departments SET department_head = ? WHERE LOWER(TRIM(department_name)) = 'information technology'";
        $stmtDept = mysqli_prepare($conn, $updateDeptQuery);
        if ($stmtDept) {
            mysqli_stmt_bind_param($stmtDept, "s", $newFullName);
            if (!mysqli_stmt_execute($stmtDept)) {
                $hasError = true;
            }
            mysqli_stmt_close($stmtDept);
        }

        // B. Update Email at Phone sa users table
        $updateUserQuery = "UPDATE users SET email = ?, phone = ? WHERE id = ?";
        $stmtUser = mysqli_prepare($conn, $updateUserQuery);
        if ($stmtUser) {
            mysqli_stmt_bind_param($stmtUser, "ssi", $email, $phone, $userId);
            if (!mysqli_stmt_execute($stmtUser)) {
                $hasError = true;
            }
            mysqli_stmt_close($stmtUser);
        }

        if (!$hasError) {
            $successMsg = "Profile updated successfully!";
        } else {
            $errorMsg = "Failed to update profile details.";
        }
    } else {
        $errorMsg = "First name and last name cannot be empty.";
    }
} 

/* ------------------------------------------------------------
   2. FETCH DEPARTMENT HEAD DETAILS
   ------------------------------------------------------------ */
$query = "SELECT 
            u.id AS user_id,
            u.username,
            u.email,
            u.phone,
            u.role,
            d.department_name,
            d.department_head
          FROM users u
          LEFT JOIN departments d ON (
              LOWER(TRIM(d.department_head)) LIKE CONCAT('%', LOWER(TRIM(u.username)), '%')
              OR u.role = 'department head'
          )
          WHERE u.id = ? 
          LIMIT 1";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$userProfile = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$userProfile) {
    echo "Profile record not found.";
    exit();
}

// Split Name into First Name & Last Name
$rawName = !empty($userProfile['department_head']) ? trim($userProfile['department_head']) : ucfirst($userProfile['username']);
$nameParts = explode(' ', $rawName);
$firstName = $nameParts[0] ?? '';
$lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

// Data Mapping
$fullName = $rawName;
$initials = strtoupper(substr($firstName, 0, 1) . ($lastName ? substr($lastName, 0, 1) : ''));

$employee = [
    'firstname'         => $firstName,
    'lastname'          => $lastName,
    'email'             => !empty($userProfile['email']) ? $userProfile['email'] : strtolower($userProfile['username']) . '@gmail.com',
    'phone'             => !empty($userProfile['phone']) ? $userProfile['phone'] : 'Not provided',
    'employee_id'       => 'N/A (Dept Head)',
    'department'        => !empty($userProfile['department_name']) ? $userProfile['department_name'] : 'Information Technology',
    'position'          => 'Department Head',
    'employment_status' => 'Active'
];

$isSelfProfile = true;
$isDepartmentHead = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/employee_profile.css">
    <title>Profile | HR Dashboard</title>
</head>

<body class="dashboard-page">
<div class="dashboard-shell">
    <?php include 'sidebar.php'; ?>

    <main class="dashboard-main">
        <div class="employee-container">

            <div class="page-header">
                <div>
                    <p class="page-kicker">Account Information</p>
                    <h1>Profile</h1>
                </div>
            </div>

            <!-- PROFILE HEADER -->
            <section class="profile-header-card">
                <div class="profile-header-content">
                    <div class="profile-photo-wrapper">
                        <div class="profile-photo">
                            <span><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                    </div>

                    <div class="profile-basic-info">
                        <h2><?php echo htmlspecialchars($fullName); ?></h2>
                        <p class="profile-position"><?php echo htmlspecialchars($employee['position']); ?></p>
                        <p class="profile-department"><?php echo htmlspecialchars($employee['department']); ?></p>

                        <div class="profile-status">
                            <span class="status-dot"></span>
                            <?php echo htmlspecialchars($employee['employment_status']); ?>
                        </div>
                    </div>

                    <div class="profile-header-action">
                        <?php 
                        $canEdit = (isset($isSelfProfile) && $isSelfProfile) || (isset($isDepartmentHead) && $isDepartmentHead);
                        ?>

                        <?php if ($canEdit): ?>
                            <button type="button" class="primary-btn" id="openEditModalBtn" onclick="openEditProfileModal()">
                                Edit Profile
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- PERSONAL INFORMATION -->
            <section class="profile-panel">
                <div class="profile-panel-header">
                    <div>
                        <p class="profile-section-kicker">Personal Information</p>
                        <h2>Basic Details</h2>
                    </div>
                </div>

                <div class="profile-info-grid">
                    <div class="profile-info-item">
                        <span class="profile-info-label">First Name</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($employee['firstname']); ?></span>
                    </div>

                    <div class="profile-info-item">
                        <span class="profile-info-label">Last Name</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($employee['lastname']); ?></span>
                    </div>

                    <div class="profile-info-item">
                        <span class="profile-info-label">Email Address</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($employee['email']); ?></span>
                    </div>

                    <div class="profile-info-item">
                        <span class="profile-info-label">Phone Number</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($employee['phone']); ?></span>
                    </div>
                </div>
            </section>

            <!-- EMPLOYMENT INFORMATION -->
            <section class="profile-panel">
                <div class="profile-panel-header">
                    <div>
                        <p class="profile-section-kicker">Employment</p>
                        <h2>Employment Information</h2>
                    </div>
                </div>

                <div class="profile-info-grid">
                    <div class="profile-info-item">
                        <span class="profile-info-label">Employee ID</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($employee['employee_id']); ?></span>
                    </div>

                    <div class="profile-info-item">
                        <span class="profile-info-label">Department</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($employee['department']); ?></span>
                    </div>

                    <div class="profile-info-item">
                        <span class="profile-info-label">Position</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($employee['position']); ?></span>
                    </div>

                    <div class="profile-info-item">
                        <span class="profile-info-label">Employment Status</span>
                        <span class="profile-info-value">
                            <span class="profile-status-badge">
                                <?php echo htmlspecialchars($employee['employment_status']); ?>
                            </span>
                        </span>
                    </div>
                </div>
            </section>

        </div>
    </main>
</div>

<!-- EDIT PROFILE MODAL -->
<?php if ($isSelfProfile): ?>
<div class="modal-overlay" id="editProfileModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Profile Details</h3>
            <button type="button" class="modal-close" id="closeEditModalBtn" style="background:none; border:none; font-size:18px; cursor:pointer;">&times;</button>
        </div>

        <form action="department_head_profile.php" method="POST">
            <input type="hidden" name="action" value="update_profile">

            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstname">First Name *</label>
                        <input type="text" id="firstname" name="firstname" class="form-control" value="<?php echo htmlspecialchars($employee['firstname']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="lastname">Last Name *</label>
                        <input type="text" id="lastname" name="lastname" class="form-control" value="<?php echo htmlspecialchars($employee['lastname']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($employee['phone'] ?? ''); ?>" placeholder="e.g. 09123456789">
                </div>
            </div>

            <div class="modal-actions">
                <button type="submit" class="primary-btn">Save Changes</button>
                <button type="button" class="btn-secondary" id="cancelEditModalBtn">Cancel</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
function openEditProfileModal() {
    const editModal = document.getElementById('editProfileModal');
    if (editModal) {
        editModal.classList.add('active');
        editModal.style.display = 'flex';
    }
}

function closeEditProfileModal() {
    const editModal = document.getElementById('editProfileModal');
    if (editModal) {
        editModal.classList.remove('active');
        editModal.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const openBtn   = document.getElementById('openEditModalBtn');
    const closeBtn  = document.getElementById('closeEditModalBtn');
    const cancelBtn = document.getElementById('cancelEditModalBtn');
    const editModal = document.getElementById('editProfileModal');

    if (openBtn) {
        openBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openEditProfileModal();
        });
    }

    if (closeBtn)  closeBtn.addEventListener('click', closeEditProfileModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeEditProfileModal);

    if (editModal) {
        editModal.addEventListener('click', function (e) {
            if (e.target === editModal) {
                closeEditProfileModal();
            }
        });
    }
});
</script>

</body>
</html>