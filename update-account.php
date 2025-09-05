<?php
// Start session and check if the user is logged in
require_once "config.php";
include("session-checker.php");

// Process update
if(isset($_POST['btnsubmit'])) { 
    $sql = "UPDATE tblaccounts SET password = ?, usertype = ?, status = ? WHERE username = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {   
        mysqli_stmt_bind_param(
            $stmt, 
            "ssss", 
            $_POST['txtpassword'], 
            $_POST['cmbtype'], 
            $_POST['rbstatus'], 
            $_GET['username']
        );
        if(mysqli_stmt_execute($stmt)) {
            // Redirect after update
            header("location: accounts-management.php");
            exit();    
        } else {
            echo "<font color='red'>Error on update statement.</font>";
        }
    }
} else { 
    // Load account data for editing
    if(isset($_GET['username']) && !empty(trim($_GET['username']))) {
        $sql = "SELECT * FROM tblaccounts WHERE username = ?";
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $_GET['username']);
            if(mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if(mysqli_num_rows($result) == 1) {
                    $account = mysqli_fetch_array($result, MYSQLI_ASSOC);
                } else {
                    echo "<font color='red'>Account not found.</font>";
                    exit();
                }
            } else {
                echo "<font color='red'>Error on loading account data.</font>";
                exit();
            }
        }
    } else {
        echo "<font color='red'>No username parameter provided in URL.</font>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Account - Equipment Management</title>
    <!-- Import Font Awesome for eye icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #0d1117;
            color: #fff;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }
        .box {
            background: #161b22;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            z-index: 10;
            position: relative;
            width: 350px;
        }
        h2 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 22px;
            text-align: center;
        }
        p {
            text-align: center;
            font-size: 14px;
            color: #aaa;
        }
        input, select {
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: none;
            width: 100%;
            font-size: 14px;
        }
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-wrapper input {
            width: 100%;
            padding-right: 35px;
        }
        .password-wrapper span {
            position: absolute;
            right: 10px;
            cursor: pointer;
            color: #aaa;
        }
        .radio-container {
            margin: 10px 0;
            display: flex;
            gap: 20px;   
            align-items: center;
        }
        .radio-container label {
            display: flex;
            align-items: center;
            gap: 5px;  
        }
        input[type=submit], a.btn {
            background: #238636;
            color: #fff;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            margin-right: 10px;
            transition: 0.3s;
            font-size: 14px;
            border: none;
        }
        input[type=submit]:hover {
            background: #2ea043;
        }
        a.btn {
            background: #d73a49;
        }
        a.btn:hover {
            background: #f85149;
        }
        .machine {
            position: absolute;
            width: 80px;
            height: 40px;
            background: #ff9800;
            top: 50%;
            left: -100px;
            border-radius: 8px;
            animation: moveMachine 6s linear infinite;
        }
        @keyframes moveMachine {
            0% { left: -100px; }
            50% { left: 100%; }
            100% { left: -100px; }
        }
    </style>
</head>
<body>
    <div class="machine"></div>

    <div class="box">
        <h2>Update Account</h2>
        <p>Change the values below and click Update.</p>
        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="POST">
            <strong>Username:</strong> 
            <?php echo isset($account['username']) ? htmlspecialchars($account['username']) : ''; ?> 
            <br><br>

            <strong>Password:</strong>
            <div class="password-wrapper">
                <input type="password" name="txtpassword" id="txtpassword" 
                    value="<?php echo isset($account['password']) ? htmlspecialchars($account['password']) : ''; ?>" required>
                <span id="togglePassword"><i class="fa-solid fa-eye-slash"></i></span>
            </div><br>

            <strong>Current User type:</strong> 
            <?php echo isset($account['usertype']) ? htmlspecialchars($account['usertype']) : ''; ?> 
            <br>
            <label for="cmbtype"><strong>Change User type to:</strong></label>
            <select name="cmbtype" id="cmbtype" required>
                <option value="">--Select Account Type--</option>
                <option value="Administrator">Administrator</option>
                <option value="Technical">Technical</option>
                <option value="User">User</option>
            </select><br>

            <div class="radio-container">
                <?php
                    if (isset($account['status']) && $account['status'] == 'Active') {
                        echo '<label><input type="radio" name="rbstatus" value="Active" checked> Active</label>';
                        echo '<label><input type="radio" name="rbstatus" value="Inactive"> Inactive</label>';
                    } else {
                        echo '<label><input type="radio" name="rbstatus" value="Active"> Active</label>';
                        echo '<label><input type="radio" name="rbstatus" value="Inactive" checked> Inactive</label>';
                    }
                ?>
            </div>

            <input type="submit" name="btnsubmit" value="Update">
            <a href="accounts-management.php" class="btn">Cancel</a>
        </form>
    </div>

    <script>
        const passwordField = document.getElementById("txtpassword");
        const togglePasswordBtn = document.getElementById("togglePassword").querySelector("i");

        document.getElementById("togglePassword").addEventListener("click", function () {
            if (passwordField.type === "password") {
                passwordField.type = "text";
                togglePasswordBtn.classList.remove("fa-eye-slash");
                togglePasswordBtn.classList.add("fa-eye");
            } else {
                passwordField.type = "password";
                togglePasswordBtn.classList.remove("fa-eye");
                togglePasswordBtn.classList.add("fa-eye-slash");
            }
        });
    </script>
</body>
</html>
