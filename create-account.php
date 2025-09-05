<?php
include("session-checker.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: black;
            font-family: Arial, sans-serif;
        }

        .form-container {
            background: rgba(0, 0, 0, 0.9);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 30px yellow;
            text-align: center;
            color: white;
            width: 320px;
            position: relative;
        }

        .form-container h2 {
            margin-bottom: 20px;
        }

        .form-container input,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            outline: none;
            font-size: 14px;
        }

        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 40px;
            box-sizing: border-box;
        }

        .password-wrapper span {
            position: absolute;
            right: 10px;
            cursor: pointer;
            color: black;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            font-weight: bold;
        }

        .submit-btn {
            background: yellow;
            color: black;
        }

        .cancel-btn {
            background: yellow;
            color: black;
            margin-top: 10px;
            width: 100px;
        }

        .cancel-btn:hover, .submit-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create Account</h2>
        <form action="create-account-process.php" method="POST">
            <input type="text" name="txtusername" placeholder="Username" required>

            <div class="password-wrapper">
                <input type="password" name="txtpassword" id="txtpassword" placeholder="Password" required>
                <span id="togglePassword"><i class="fa fa-eye-slash"></i></span>
            </div>

            <select name="txtusertype" required>
                <option value="">--Select account type--</option>
                <option value="Administrator">Administrator</option>
                <option value="Technical">Technical</option>
                <option value="User">User</option>
            </select>

            <button type="submit" class="submit-btn">Submit</button>
        </form>
        <button onclick="window.location.href='dashboard.php'" class="cancel-btn">Cancel</button>
    </div>

    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const passwordInput = document.querySelector("#txtpassword");
        const icon = togglePassword.querySelector("i");

        togglePassword.addEventListener("click", () => {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        });
    </script>
</body>
</html>
