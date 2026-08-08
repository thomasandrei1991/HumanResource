<?php
    session_start();
    include "database.php"; 
    $modalMessage = '';
    $modalTitle = 'Registration';
    $employeesSql = " SELECT id, employee_id, firstname, lastname FROM employees ORDER BY firstname ASC "; 
    $employeesResult = mysqli_query($conn, $employeesSql); 
    
    if (isset($_SESSION['register_error'])) {
        $modalMessage = $_SESSION['register_error'];
        $modalTitle = 'Registration Failed';
        unset($_SESSION['register_error']);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/register.css">
    <title>Registration Form</title>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form id="registerForm" action="register_process.php" method="POST">
                <div class="img-group">
                    <img src="images/ama_logo.png" alt="logo">
                </div>

                
                <div class="input-group">
                    <label for="fullname">Full Name:</label>
                    <input
                        type="text"
                        id="fullname"
                        class="inputs"
                        name="fullname"
                        required
                    >
                </div>

                <div class="input-group">
                    <label for="employee_id">Employee:</label>
                    <select
                        id="employee_id"
                        name="employee_id"
                        class="inputs"
                        required
                    >
                        <option value="">Select Employee</option>

                        <?php
                        if (mysqli_num_rows($employeesResult) > 0) {

                            while ($employee = mysqli_fetch_assoc($employeesResult)) {
                        ?>

                            <option value="<?php echo $employee['id']; ?>">
                                <?php
                                echo htmlspecialchars(
                                    $employee['firstname'] . ' ' . $employee['lastname']
                                );
                                ?>
                            </option>

                        <?php
                            }

                        } else {
                        ?>

                            <option value="" disabled>
                                No employees available
                            </option>

                        <?php } ?>

                    </select>
                </div>

                <!-- USERNAME -->
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input
                        type="text"
                        id="username"
                        class="inputs"
                        name="username"
                        required
                    >
                </div>

                <!-- PASSWORD -->
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input
                        type="password"
                        id="password"
                        class="inputs"
                        name="password"
                        required
                    >
                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="input-group">
                    <label for="confirmPassword">Confirm Password:</label>
                    <input
                        type="password"
                        id="confirmPassword"
                        class="inputs"
                        name="confirmPassword"
                        required
                    >
                </div>

                <!-- SHOW PASSWORD -->
                <div class="showPassword">
                    <input
                        type="checkbox"
                        id="showPassword"
                        name="showPassword"
                    >

                    <label for="showPassword">
                        Show Password
                    </label>
                </div>

                <!-- SUBMIT -->
                <input
                    type="submit"
                    name="register"
                    value="CREATE ACCOUNT"
                    id="btn-submit"
                >

                <!-- LOGIN LINK -->
                <div class="links">
                    <p>
                        Already have an account?
                        <a href="login.php">Sign in</a>
                    </p>
                </div>

                </form>
            </div>
        </div>

        <!-- ========================== -->
        <!-- REGISTRATION MODAL -->
        <!-- ========================== -->

        <div
            id="errorModal"
            class="modal hidden"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modalTitle"
        >
            <div class="modal-content">

                <h3 id="modalTitle">
                    Registration
                </h3>

                <p id="modalMessage">
                    Please complete the form.
                </p>

                <button
                    type="button"
                    id="closeModalBtn"
                >
                    Close
                </button>

            </div>
        </div>

        <script>
            window.onload = function () {

                const message =
                    <?php echo json_encode($modalMessage); ?>;

                const title =
                    <?php echo json_encode($modalTitle); ?>;

                if (message) {

                    document.getElementById('modalTitle').textContent =
                        title;

                    document.getElementById('modalMessage').textContent =
                        message;

                    document
                        .getElementById('errorModal')
                        .classList.remove('hidden');
                }
            };
        </script>

        <script src="script.js"></script>

</body>
</html>
