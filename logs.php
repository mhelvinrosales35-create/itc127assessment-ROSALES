<?php
// Start session and check if logged in
require_once "config.php";
include("session-checker.php");

// Fetch logs from database
$logs = [];
$sql = "SELECT * FROM tblequipmentslogs ORDER BY id DESC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $logs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Equipment Logs</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url("jungle.jpg") no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        .overlay {
            background: rgba(0,0,0,0.7);
            min-height: 100vh;
            padding: 20px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .top-bar h1 {
            margin: 0;
            color: #FFD700;
        }

        .top-bar a {
            color: #FFD700;
            text-decoration: none;
            font-weight: bold;
            margin: 0 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255,255,255,0.9);
            color: black;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        tr:hover {
            background: #ddd;
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="top-bar">
        <a href="dashboard.php">⬅ Back to Dashboard</a>
        <h1>🌴 Equipment Logs 🌴</h1>
        <a href="logout.php">Logout</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Time</th>
            <th>Asset Number</th>
            <th>Action</th>
            <th>Module</th>
            <th>Performed By</th>
        </tr>
        <?php if (count($logs) > 0): ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['id']); ?></td>
                    <td><?php echo htmlspecialchars($log['datelog']); ?></td>
                    <td><?php echo htmlspecialchars($log['timelog']); ?></td>
                    <td><?php echo htmlspecialchars($log['assetnumber']); ?></td>
                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                    <td><?php echo htmlspecialchars($log['module']); ?></td>
                    <td><?php echo htmlspecialchars($log['performedby']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No logs found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
