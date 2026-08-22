<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/login.css">
    <title>Login Form</title>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form id="loginForm" action="login_process.php" method="POST">
                <div class="img-group">
                    <img src="images/ama_logo.png" alt="logo">
                </div>
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" class="inputs" name="username">
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" class="inputs" name="password">
                </div>
                <div class="showPassword">
                    <input type="checkbox" id="showPassword" name="showPassword">
                    <label for="showPassword">Show Password</label>
                </div>
                <input type="submit" name="login" value="SIGN IN" id="btn-submit">
                <div class="links">
                    <p>Forgot <a href="forgot-password.php">Username / Password?</a></p>
                    <p>Don't have an account? <a href="register.php">Sign up</a></p>
                </div>
            </form>
        </div>
    </div>

    <div id="errorModal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-content">
            <h3 id="modalTitle">Login Failed</h3>
            <p id="modalMessage">Incorrect username or password.</p>
            <button type="button" id="closeModalBtn">Close</button>
        </div>
    </div>
    <?php if(isset($_SESSION['login_error'])): ?>

    <script>
        window.onload = function () {
            document.getElementById("modalMessage").textContent = "<?php echo $_SESSION['login_error']; ?>";
            document.getElementById("errorModal").classList.remove("hidden");
        }
    </script>
    <?php
        unset($_SESSION['login_error']);
        endif;
    ?>
    <script src="script.js"></script>
    <?php if(isset($_SESSION['register_success'])): ?>
    <script>
        window.onload = function () {
            document.getElementById("modalTitle").textContent = "Registration Successful";
            document.getElementById("modalMessage").textContent = "<?php echo $_SESSION['register_success']; ?>";
            document.getElementById("errorModal").classList.remove("hidden");
        }
    </script>
    <?php
        unset($_SESSION['register_success']);
        endif;
    ?>
</body>
</html>