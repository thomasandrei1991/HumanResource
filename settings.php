<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Settings | HR Dashboard</title>
</head>
<body class="dashboard-page">
    <div class="dashboard-shell">
        <?php include 'sidebar.php'; ?>
        <main class="dashboard-main">
            <div class="dashboard-container">
                <div class="employee-container">
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">System Configuration</p>
                            <h1>Settings</h1>
                        </div>
                        <button class="primary-btn">Save Changes</button>
                    </div>

                    <div class="settings-grid">
                        <div class="employee-panel">
                            <div class="panel-header">
                                <h2>System Preferences</h2>
                            </div>
                            <div class="settings-list">
                                <div class="setting-item">
                                    <div>
                                        <strong>Company Profile</strong>
                                        <p>Manage organization name, logo, and contact details.</p>
                                    </div>
                                    <a href="#">Edit</a>
                                </div>
                                <div class="setting-item">
                                    <div>
                                        <strong>Attendance Rules</strong>
                                        <p>Configure work hours, grace periods, and late policy.</p>
                                    </div>
                                    <a href="#">Manage</a>
                                </div>
                                <div class="setting-item">
                                    <div>
                                        <strong>Leave Policies</strong>
                                        <p>Set leave entitlement, approval stages, and holidays.</p>
                                    </div>
                                    <a href="#">Manage</a>
                                </div>
                                <div class="setting-item">
                                    <div>
                                        <strong>Payroll Settings</strong>
                                        <p>Control pay schedules, tax rules, and deductions.</p>
                                    </div>
                                    <a href="#">Configure</a>
                                </div>
                            </div>
                        </div>

                        <div class="employee-panel">
                            <div class="panel-header">
                                <h2>Security & Access</h2>
                            </div>
                            <div class="settings-list">
                                <div class="setting-item">
                                    <div>
                                        <strong>Admin Accounts</strong>
                                        <p>Review active system administrators and their roles.</p>
                                    </div>
                                    <span class="setting-pill">Secure</span>
                                </div>
                                <div class="setting-item">
                                    <div>
                                        <strong>Password Policy</strong>
                                        <p>Enforce stronger passwords and reset requirements.</p>
                                    </div>
                                    <span class="setting-pill">Enabled</span>
                                </div>
                                <div class="setting-item">
                                    <div>
                                        <strong>Audit Log</strong>
                                        <p>Track changes made across records and modules.</p>
                                    </div>
                                    <span class="setting-pill">Active</span>
                                </div>
                                <div class="setting-item">
                                    <div>
                                        <strong>Backup Sync</strong>
                                        <p>Keep HR data protected and synchronized.</p>
                                    </div>
                                    <span class="setting-pill">Healthy</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
