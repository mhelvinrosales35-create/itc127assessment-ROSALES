<?php
include ("session-checker.php");
?>

<html>
<head>
    <title>Dashboard - Equipment Management System</title>
    <style>
        /* Body reset */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: Arial, sans-serif;
            color: white;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
        }

        /* Galaxy stars container */
        .custom-container {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            opacity: 0.8;
            animation: twinkle 5s infinite;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.8; transform: scale(1); }
            50% { opacity: 0.2; transform: scale(0.5); }
        }

        /* Dashboard content box */
        .dashboard-content {
            position: relative;
            z-index: 1;
            background: rgba(0, 0, 30, 0.8);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            width: 400px;
            box-shadow: 0 0 25px rgba(0, 0, 255, 0.6);
            backdrop-filter: blur(6px);
        }

        .dashboard-content h1 {
            margin-bottom: 10px;
            font-size: 24px;
            color: #00d4ff;
        }

        .dashboard-content h2 {
            margin-bottom: 20px;
            font-size: 20px;
            color: #ff6ec7;
        }

        .dashboard-buttons button {
            display: block;
            margin: 10px auto;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #00d4ff, #0077ff);
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            width: 80%;
        }

        .dashboard-buttons button:hover {
            background: linear-gradient(135deg, #ff6ec7, #ff3d7f);
            transform: scale(1.05);
        }

        .logout-link {
            display: inline-block;
            margin-top: 20px;
            color: #ff6ec7;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .logout-link:hover {
            color: #ff3d7f;
        }
    </style>
</head>
<body>
    <div class="custom-container"></div>

    <div class="dashboard-content">
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <h2>Dashboard</h2>

        <div class="dashboard-buttons">
            <button onclick="location.href='equipments.php'">Equipments</button>
            <button onclick="location.href='logs.php'">Logs</button>
            <button onclick="location.href='accounts-management.php'">Accounts Management</button>
        </div>

        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <script>
        const container = document.querySelector('.custom-container');

        // Generate random stars
        for (let i = 0; i < 150; i++) {
            let star = document.createElement('div');
            star.classList.add('star');

            let size = Math.random() * 3 + 1;
            let xPos = Math.random() * window.innerWidth;
            let yPos = Math.random() * window.innerHeight;
            let duration = Math.random() * 5 + 3;

            star.style.width = `${size}px`;
            star.style.height = `${size}px`;
            star.style.left = `${xPos}px`;
            star.style.top = `${yPos}px`;
            star.style.animationDuration = `${duration}s`;

            container.appendChild(star);
        }
    </script>
</body>
</html>
