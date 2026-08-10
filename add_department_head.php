<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userRole = $_SESSION['role'] ?? '';

if ($userRole !== 'Admin') {
    header("Location: dashboard.php");
    exit();
}

require_once 'database.php';


// ==========================
// GET ACTIVE DEPARTMENTS
// ==========================

$departmentQuery = mysqli_query(
    $conn,
    "SELECT id, department_name
     FROM departments
     WHERE status = 'Active'
     ORDER BY department_name ASC"
);


// ==========================
// MODAL MESSAGE
// ==========================

$modalTitle = '';
$modalMessage = '';

if (isset($_SESSION['register_success'])) {

    $modalTitle = 'Success';
    $modalMessage = $_SESSION['register_success'];

    unset($_SESSION['register_success']);

}

if (isset($_SESSION['register_error'])) {

    $modalTitle = 'Registration Failed';
    $modalMessage = $_SESSION['register_error'];

    unset($_SESSION['register_error']);

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link
        rel="stylesheet"
        href="styles/common.css"
    >

    <title>Register Department Head</title>

</head>

<body class="dashboard-page">

<div class="dashboard-shell">

    <?php include 'sidebar.php'; ?>

    <main class="dashboard-main">

        <div class="dashboard-container">

            <div class="employee-form-panel">

                <div class="panel-header">

                    <h2>
                        Create Department Head Account
                    </h2>

                </div>


                <form
                    action="add_department_head_process.php"
                    method="POST"
                >


                    <!-- ==========================
                        DEPARTMENT
                    ========================== -->

                    <div class="input-group">

                        <label for="department_id">
                            Department
                        </label>

                        <select
                            id="department_id"
                            name="department_id"
                            class="inputs"
                            required
                        >

                            <option
                                value=""
                                disabled
                                selected
                            >
                                Select department
                            </option>

                            <?php

                            if (
                                $departmentQuery &&
                                mysqli_num_rows($departmentQuery) > 0
                            ):

                                while (
                                    $department =
                                    mysqli_fetch_assoc($departmentQuery)
                                ):

                            ?>

                                <option
                                    value="<?php echo $department['id']; ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $department['department_name']
                                    );
                                    ?>

                                </option>

                            <?php

                                endwhile;

                            else:

                            ?>

                                <option
                                    value=""
                                    disabled
                                >
                                    No active departments available
                                </option>

                            <?php endif; ?>

                        </select>

                    </div>


                    <!-- ==========================
                        FULL NAME
                    ========================== -->

                    <div class="input-group">

                        <label for="fullname">
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="fullname"
                            name="fullname"
                            class="inputs"
                            placeholder="Enter department head name"
                            required
                        >

                    </div>


                    <!-- ==========================
                        USERNAME
                    ========================== -->

                    <div class="input-group">

                        <label for="username">
                            Username
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="inputs"
                            placeholder="Enter username"
                            required
                        >

                    </div>


                    <!-- ==========================
                        PASSWORD
                    ========================== -->

                    <div class="input-group">

                        <label for="password">
                            Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="inputs"
                            placeholder="Enter password"
                            required
                        >

                    </div>


                    <!-- ==========================
                        CONFIRM PASSWORD
                    ========================== -->

                    <div class="input-group">

                        <label for="confirmPassword">
                            Confirm Password
                        </label>

                        <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            class="inputs"
                            placeholder="Confirm password"
                            required
                        >

                    </div>


                    <!-- ==========================
                        ACTIONS
                    ========================== -->

                    <div class="form-actions">

                        <button
                            type="submit"
                            class="primary-btn"
                        >
                            Create Account
                        </button>

                        <a
                            href="dashboard.php"
                            class="primary-btn"
                        >
                            Cancel
                        </a>

                    </div>

                </form>
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

            </div>

        </div>

    </main>

</div>
<script>

    document.addEventListener('DOMContentLoaded', function () {

        const modal = document.getElementById('errorModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const closeModalBtn = document.getElementById('closeModalBtn');

        const title = <?php echo json_encode($modalTitle); ?>;
        const message = <?php echo json_encode($modalMessage); ?>;

        if (title && message && modal) {

            modalTitle.textContent = title;
            modalMessage.textContent = message;

            modal.classList.remove('hidden');

        }

        if (closeModalBtn) {

            closeModalBtn.addEventListener('click', function () {

                modal.classList.add('hidden');

            });

        }

    });

</script>
<script src="script.js"></script>
</body>

</html>