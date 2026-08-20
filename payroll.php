<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'database.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$userRole = $_SESSION['role'] ?? '';

// ==========================================
// 1. DATA QUERIES PARA SA SUMMARY CARDS
// ==========================================

// Total Active Employees
$totalEmpQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'Active'");
$totalEmployees = mysqli_fetch_assoc($totalEmpQuery)['total'] ?? 0;

// Suriin kung umiiral ang payrolls table sa database
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'payrolls'");
$hasPayrollTable = ($tableCheck && mysqli_num_rows($tableCheck) > 0);

$paidThisMonth = 0;
$pendingThisMonth = 0;
$totalPayrollAmount = 0;

if ($hasPayrollTable) {
    // Bilang ng Paid ngayong buwan
    $paidQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM payrolls WHERE status = 'Paid' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $paidThisMonth = mysqli_fetch_assoc($paidQuery)['total'] ?? 0;

    // Bilang ng Pending ngayong buwan
    $pendingQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM payrolls WHERE status = 'Pending' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $pendingThisMonth = mysqli_fetch_assoc($pendingQuery)['total'] ?? 0;

    // Kabuuang Net Salary
    $totalAmountQuery = mysqli_query($conn, "SELECT SUM(net_salary) AS total FROM payrolls WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $totalPayrollAmount = mysqli_fetch_assoc($totalAmountQuery)['total'] ?? 0;
} else {
    // Kung wala pang payrolls table, gagamitin muna ang tantya mula sa employees table
    $salarySumQuery = mysqli_query($conn, "SELECT SUM(salary) AS total FROM employees WHERE employment_status = 'Active'");
    $totalPayrollAmount = mysqli_fetch_assoc($salarySumQuery)['total'] ?? 0;
}

// Function para sa initials avatar
// Maglagay ng string type hint at ?string para tumanggap din ng null values
function getInitials(?string $firstname, ?string $lastname): string {
    $first = !empty($firstname) ? mb_substr($firstname, 0, 1) : '';
    $last = !empty($lastname) ? mb_substr($lastname, 0, 1) : '';
    return strtoupper($first . $last);
}

// Array ng mga kulay para sa avatar
$bgColors = ['blue-bg', 'green-bg', 'orange-bg', 'purple-bg'];

// ==========================================
// 2. QUERY PARA SA PAYROLL RECORDS TABLE
// ==========================================
if ($hasPayrollTable) {
    $payrollListQuery = mysqli_query($conn, "
        SELECT 
            p.*, 
            e.firstname, 
            e.lastname 
        FROM payrolls p 
        JOIN employees e ON p.employee_id = e.id 
        ORDER BY p.id DESC
    ");
} else {
    // Fallback: Kunin ang listahan mula sa employees table kung wala pa ang payrolls table
    $payrollListQuery = mysqli_query($conn, "
        SELECT 
            id AS employee_db_id,
            firstname, 
            lastname, 
            salary AS basic_salary, 
            0.00 AS overtime, 
            0.00 AS deductions, 
            salary AS net_salary, 
            'Pending' AS status 
        FROM employees 
        WHERE employment_status = 'Active' 
        ORDER BY id DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/payroll.css">
    <title>Payroll | HR Dashboard</title>
</head>
<body class="dashboard-page">
    <div class="dashboard-shell">
        <?php include 'sidebar.php'; ?>
        <main class="dashboard-main">
            <div class="dashboard-container">
                <div class="attendance-container">
                    
                    <!-- Page Header -->
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">Salary Management</p>
                            <h1>Payroll</h1>
                        </div>
                        <button class="primary-btn">
                            + Generate Payroll
                        </button>
                    </div>

                    <!-- Dynamic Summary Cards -->
                    <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Employees</h3>
                            <p><?php echo number_format($totalEmployees); ?></p>
                        </div>
                        <div class="summary-card green">
                            <h3>Paid This Month</h3>
                            <p><?php echo number_format($paidThisMonth); ?></p>
                        </div>
                        <div class="summary-card orange">
                            <h3>Pending</h3>
                            <p><?php echo number_format($pendingThisMonth); ?></p>
                        </div>
                        <div class="summary-card purple">
                            <h3>Total Payroll</h3>
                            <p>₱<?php echo number_format($totalPayrollAmount, 2); ?></p>
                        </div>
                    </div>

                    <!-- Panel Header -->
                    <div class="panel-header">
                        <h2>Payroll Records</h2>
                    </div>

                    <!-- Dynamic Payroll Table -->
                    <div class="employee-panel">
                        <table class="dashboard-table employee-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Basic Salary</th>
                                    <th>Overtime</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($payrollListQuery && mysqli_num_rows($payrollListQuery) > 0): ?>
                                    <?php 
                                    $colorIndex = 0;
                                    while ($row = mysqli_fetch_assoc($payrollListQuery)): 
                                        $fullName = htmlspecialchars(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
                                        $initials = getInitials($row['firstname'] ?? '', $row['lastname'] ?? '');
                                        $avatarColor = $bgColors[$colorIndex % count($bgColors)];
                                        $colorIndex++;

                                        $basicSalary = floatval($row['basic_salary'] ?? 0);
                                        $overtime = floatval($row['overtime'] ?? 0);
                                        $deductions = floatval($row['deductions'] ?? 0);
                                        $netSalary = floatval($row['net_salary'] ?? ($basicSalary + $overtime - $deductions));
                                        $status = $row['status'] ?? 'Pending';
                                        
                                        // Badge class batay sa status
                                        $badgeClass = ($status === 'Paid') ? 'present' : 'pending';
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="employee-name">
                                                    <div class="emp-avatar <?php echo $avatarColor; ?>">
                                                        <?php echo $initials; ?>
                                                    </div>
                                                    <?php echo $fullName; ?>
                                                </div>
                                            </td>
                                            <td>₱<?php echo number_format($basicSalary, 2); ?></td>
                                            <td>₱<?php echo number_format($overtime, 2); ?></td>
                                            <td>₱<?php echo number_format($deductions, 2); ?></td>
                                            <td><strong>₱<?php echo number_format($netSalary, 2); ?></strong></td>
                                            <td>
                                                <span class="status-badge <?php echo $badgeClass; ?>">
                                                    <?php echo htmlspecialchars($status); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 20px; color: #6b7280;">
                                            No payroll records found.
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
    <script src="script.js"></script>
</body>
</html>