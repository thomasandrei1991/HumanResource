<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header("Location: login.php");
    exit();
}

require_once 'database.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: department_heads.php");
    exit();
}

// Kunin ang kasalukuyang data ng Department Head
$stmt = $conn->prepare("
    SELECT 
        u.id AS user_id, 
        u.fullname, 
        u.username, 
        d.id AS dept_id, 
        d.status AS dept_status 
    FROM users u 
    LEFT JOIN departments d ON d.department_head = u.fullname 
    WHERE u.id = ? AND u.role = 'Department Head'
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: department_heads.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $status = $_POST['status'] ?? 'Inactive';

    if (empty($fullname) || empty($username)) {
        $error = "Paki-punan ang lahat ng kinakailangang field.";
    } else {
        $updateUser = $conn->prepare("UPDATE users SET fullname = ?, username = ? WHERE id = ?");
        $updateUser->bind_param("ssi", $fullname, $username, $id);

        if ($updateUser->execute()) {
            if (!empty($user['dept_id'])) {
                $updateDept = $conn->prepare("UPDATE departments SET department_head = ?, status = ? WHERE id = ?");
                $updateDept->bind_param("ssi", $fullname, $status, $user['dept_id']);
                $updateDept->execute();
            } else {
                $updateDept = $conn->prepare("UPDATE departments SET status = ? WHERE department_head = ?");
                $updateDept->bind_param("ss", $status, $fullname);
                $updateDept->execute();
            }

            header("Location: department_heads.php");
            exit();
        } else {
            $error = "Nagkaroon ng error sa pag-update: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/department_heads.css">
    <title>Edit Department Head | HR Dashboard</title>
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
                        <h1>Edit Department Head</h1>
                    </div>
                </div>

                <!-- ==========================
                    FORM (WITHOUT BOX PANEL)
                ========================== -->
                <form method="POST">
                    <?php if ($error): ?>
                        <div class="error-message" style="color: #dc2626; margin-bottom: 15px;">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div style="margin-bottom: 16px;">
                        <label for="fullname" style="display: block; font-weight: 600; margin-bottom: 6px;">Full Name</label>
                        <input 
                            type="text" 
                            id="fullname" 
                            name="fullname" 
                            value="<?php echo htmlspecialchars($user['fullname']); ?>" 
                            required
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label for="username" style="display: block; font-weight: 600; margin-bottom: 6px;">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            value="<?php echo htmlspecialchars($user['username']); ?>" 
                            required
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label for="status" style="display: block; font-weight: 600; margin-bottom: 6px;">Status</label>
                        <select 
                            id="status" 
                            name="status"
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; background-color: #fff;">
                            <option value="Active" <?php echo ($user['dept_status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Inactive" <?php echo ($user['dept_status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="action-buttons">
                        <button type="submit" class="primary-btn">Save Changes</button>
                        <button type="button" class="primary-btn" onclick="window.location.href='department_heads.php'">Cancel</button>
                    </div>
                </form>

            </div>
        </div>
    </main>
</div>
</body>
</html>