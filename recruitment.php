<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/recruitment.css">
    <title>Recruitment | HR Dashboard</title>
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
                            <p class="page-kicker">Hiring & Recruitment</p>
                            <h1>Recruitment</h1>
                        </div>

                        <button class="primary-btn">
                            + Add Applicant
                        </button>
                    </div>
                    <!-- Summary Cards -->
                    <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Total Applicants</h3>
                            <p>84</p>
                        </div>
                        <div class="summary-card orange">
                            <h3>For Interview</h3>
                            <p>18</p>
                        </div>
                        <div class="summary-card green">
                            <h3>Hired</h3>
                            <p>12</p>
                        </div>
                        <div class="summary-card red">
                            <h3>Rejected</h3>
                            <p>54</p>
                        </div>
                    </div>
                    <!-- Payroll Table -->
                    <div class="employee-panel">
                        <div class="panel-header">
                            <h2>Applicant List</h2>
                            <a href="#" class="view-all">
                                Export →
                            </a>
                        </div>
                        <table class="dashboard-table employee-table">
                            <thead>
                                <tr>
                                    <th>Applicant</th>
                                    <th>Position Applied</th>
                                    <th>Application Date</th>
                                    <th>Interview</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar blue-bg">JM</div>
                                            John Miller
                                        </div>
                                    </td>
                                    <td>Software Engineer</td>
                                    <td>July 28, 2026</td>
                                    <td>August 2, 2026</td>
                                    <td>
                                        <span class="status-badge pending">
                                            For Interview
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar green-bg">AS</div>
                                            Anna Smith
                                        </div>
                                    </td>
                                    <td>HR Assistant</td>
                                    <td>July 24, 2026</td>
                                    <td>July 30, 2026</td>
                                    <td>
                                        <span class="status-badge present">
                                            Hired
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar orange-bg">DL</div>
                                            David Lee
                                        </div>
                                    </td>
                                    <td>Accountant</td>
                                    <td>July 20, 2026</td>
                                    <td>July 25, 2026</td>
                                    <td>
                                        <span class="status-badge absent">
                                            Rejected
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>   
        </main>
    </div>
</body>
</html>
