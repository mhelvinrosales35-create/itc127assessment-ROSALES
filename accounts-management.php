<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accounts Management Page - Equipment Management System</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: Arial, sans-serif;
            background: #0d0d2b;
            color: #fff;
        }

        .custom-container {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: -1;
        }

        .raindrop {
            position: absolute;
            width: 2px;
            height: 15px;
            background: rgba(173, 216, 230, 0.7);
            animation: fall linear infinite;
        }

        @keyframes fall {
            to {
                transform: translateY(100vh);
            }
        }

        .content-box {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.7);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 0 15px rgba(255,255,255,0.2);
            width: 80%;
            max-width: 900px;
        }

        .account-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .account-table th, .account-table td {
            border: 1px solid #555;
            padding: 8px;
        }

        .account-table th {
            background: #222;
        }

        .search-form {
            margin-bottom: 15px;
        }

        .search-input {
            padding: 5px;
            border-radius: 5px;
            border: none;
        }

        .search-btn, .create-account-link, .back-link {
            padding: 6px 12px;
            border-radius: 5px;
            background: #1e90ff;
            color: #fff;
            text-decoration: none;
            margin: 5px;
            display: inline-block;
        }

        .edit-link, .delete-link {
            margin-right: 10px;
            color: #1e90ff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="custom-container"></div>

    <div class="content-box">
        <?php
        session_start();
        if (isset($_SESSION['username'])) {
            echo "<h1>Welcome, " . htmlspecialchars($_SESSION['username']) . "</h1>";
            echo "<h4>Account type: " . htmlspecialchars($_SESSION['usertype']) . "</h4>";
        } else {
            header("location: login.php");
            exit;
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="search-form">
            <a href="create-account.php" class="create-account-link">Create New Account</a>
            <a href="dashboard.php" class="back-link">Back</a>
            <input type="text" name="txtsearch" placeholder="Search by Username or Usertype" class="search-input">
            <input type="submit" name="btnsearch" value="Search" class="search-btn">
        </form>

        <?php
        function buildtable($result) {
            if (mysqli_num_rows($result) > 0) {
                echo "<table class='account-table'>";
                echo "<tr><th>Username</th><th>Usertype</th><th>Status</th><th>Created By</th><th>Date Created</th><th>Actions</th></tr>";
                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['usertype']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['createdby']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['datecreated']) . "</td>";
                    echo "<td>";
                    echo "<a href='update-account.php?username=" . urlencode($row['username']) . "' class='edit-link'>Edit</a>";
                    echo "<a href='delete-account.php?username=" . urlencode($row['username']) . "' 
                          class='delete-link' 
                          onclick=\"return confirm('Are you sure you want to delete this account?');\">Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No records found.</p>";
            }
        }

        require_once "config.php";

        if (isset($_POST['btnsearch'])) {
            $sql = "SELECT * FROM tblaccounts WHERE username LIKE ? OR usertype LIKE ? ORDER BY username";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                $searchvalue = '%' . $_POST['txtsearch'] . '%';
                mysqli_stmt_bind_param($stmt, "ss", $searchvalue, $searchvalue);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                buildtable($result);
            } else {
                echo "<p style='color:red;'>Error loading data from table.</p>";
            }
        } else {
            $sql = "SELECT * FROM tblaccounts ORDER BY username";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                buildtable($result);
            } else {
                echo "<p style='color:red;'>Error loading data from table.</p>";
            }
        }
        ?>
    </div>

    <script>
        const container = document.querySelector('.custom-container');

        for (let i = 0; i < 100; i++) {
            let drop = document.createElement('div');
            drop.classList.add('raindrop');

            let xPos = Math.random() * window.innerWidth;
            let delay = Math.random() * 5;
            let duration = Math.random() * 3 + 2;

            drop.style.left = `${xPos}px`;
            drop.style.animationDuration = `${duration}s`;
            drop.style.animationDelay = `${delay}s`;

            container.appendChild(drop);
        }
    </script>
</body>
</html>
