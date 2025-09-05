<?php
session_start();

// Check if the form is submitted using the button
if (isset($_POST['btnlogin'])) {
    require_once "config.php"; 

    $sql = "SELECT * FROM tblaccounts WHERE username = ? AND password = ? AND status = 'Active'";

    if ($stmt = mysqli_prepare($conn, $sql)) { 
        mysqli_stmt_bind_param($stmt, "ss", $_POST['txtusername'], $_POST['txtpassword']);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $account = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $_SESSION['username'] = $_POST['txtusername'];
                $_SESSION['usertype'] = $account['usertype'];

                header("location:dashboard.php");
                exit;
            } else {
                $error_message = "Incorrect login details or account is inactive.";
            }
        } else {
            $error_message = "ERROR executing the login statement.";
        }
    } else {
        $error_message = "ERROR preparing the login statement.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Page - Equipment Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Ocean Background */
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to top, #006994, #00aaff);
            overflow: hidden;
            font-family: Arial, sans-serif;
        }

        /* Waves effect */
        .wave {
            position: absolute;
            bottom: 0;
            width: 200%;
            height: 200px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 100%;
            animation: wave 6s infinite linear;
        }
        .wave:nth-child(2) {
            bottom: -20px;
            animation-duration: 8s;
            opacity: 0.5;
        }
        .wave:nth-child(3) {
            bottom: -40px;
            animation-duration: 10s;
            opacity: 0.3;
        }

        @keyframes wave {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Login Box */
        .login-form {
            position: relative;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            width: 350px;
            text-align: center;
            z-index: 1;
        }

        .login-form h1 {
            margin-bottom: 20px;
            color: #006994;
        }

        .login-form label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .password-wrapper {
            position: relative;
        }
        .password-wrapper input {
            width: 100%;
            padding-right: 40px;
        }
        .password-wrapper span {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .login-form input[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #006994;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .login-form input[type="submit"]:hover {
            background: #004d73;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <!-- Moving waves -->
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>

    <div class="login-form">
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <h1>Login</h1>
            <label for="txtusername">Username:</label>
            <input type="text" name="txtusername" id="txtusername" required>

            <label for="txtpassword">Password:</label>
            <div class="password-wrapper">
                <input type="password" name="txtpassword" id="txtpassword" required>
                <span id="eye-icon" onclick="togglePassword()">
                    <i class="fa fa-eye-slash"></i>
                </span>
            </div>

            <input type="submit" name="btnlogin" value="Login">
        </form>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("txtpassword");
            var icon = document.getElementById("eye-icon").querySelector("i");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }
    </script>

</body>
</html>
