<?php
// Start session and check if the user is logged in
require_once "config.php";
include("session-checker.php");

// Handle form submission for adding equipment
if (isset($_POST['btnadd'])) {

    // Check for duplicate assetnumber or serialnumber
    $sql = "SELECT * FROM tblequipments WHERE assetnumber = ? OR serialnumber = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $_POST['assetnumber'], $_POST['serialnumber']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 0) {
            // Insert new equipment
            $sql = "INSERT INTO tblequipments 
                        (assetnumber, serialnumber, type, manufacturer, yearmodel, description, branch, department, status, createdby, datecreated) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                $datelog = date('Y-m-d H:i:s');
                mysqli_stmt_bind_param(
                    $stmt,
                    "sssssssssss",
                    $_POST['assetnumber'],
                    $_POST['serialnumber'],
                    $_POST['type'],
                    $_POST['manufacturer'],
                    $_POST['yearmodel'],
                    $_POST['description'],
                    $_POST['branch'],
                    $_POST['department'],
                    $_POST['status'],
                    $_SESSION['username'],
                    $datelog
                );

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);

                    // Insert into logs
                    $sql = "INSERT INTO tblequipmentslogs 
                                (datelog, timelog, assetnumber, performedby, action, module) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        $datelog = date("Y-m-d");
                        $timelog = date("H:i:s");
                        $action = "Add";
                        $module = "Equipments";

                        mysqli_stmt_bind_param(
                            $stmt,
                            "ssssss",
                            $datelog,
                            $timelog,
                            $_POST['assetnumber'],
                            $_SESSION['username'],
                            $action,
                            $module
                        );

                        if (mysqli_stmt_execute($stmt)) {
                            mysqli_stmt_close($stmt);
                            header("location: equipments.php");
                            exit();
                        } else {
                            echo "<font color='red'>Error inserting log record.</font>";
                        }
                    }
                } else {
                    echo "<font color='red'>Error adding equipment. Please try again.</font>";
                }
            }
        } else {
            echo "<font color='red'>ERROR: Asset Number or Serial Number already exists.</font>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Equipment</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url("Citynight.jpeg") repeat-x bottom center;
            background-size: cover;
            animation: moveBackground 20s linear infinite;
            color: white;
            text-align: center;
        }

        h1 {
            margin-top: 20px;
        }

        form {
            background: rgba(0,0,0,0.7);
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 20px;
            text-align: left;
            width: 400px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"], .btn-back {
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 15px;
            padding: 10px;
            width: 100%;
            text-align: center;
            display: inline-block;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
        }

        input[type="submit"]:hover, .btn-back:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <h1>Add Equipment</h1>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="assetnumber">Asset Number:</label>
        <input type="text" id="assetnumber" name="assetnumber" required>

        <label for="serialnumber">Serial Number:</label>
        <input type="text" id="serialnumber" name="serialnumber" required>

        <label for="type">Type:</label>
        <select name="type" id="type" required>
            <option value="">--Select Type--</option>
            <option value="Monitor">Monitor</option>
            <option value="CPU">CPU</option>
            <option value="Keyboard">Keyboard</option>
            <option value="Mouse">Mouse</option>
            <option value="AVR">AVR</option>
            <option value="MAC">MAC</option>
            <option value="Printer">Printer</option>
            <option value="Projector">Projector</option>
        </select>

        <label for="manufacturer">Manufacturer:</label>
        <input type="text" id="manufacturer" name="manufacturer" required>

        <label for="yearmodel">Year Model:</label>
        <input type="text" id="yearmodel" name="yearmodel" maxlength="4" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4"></textarea>

        <label for="branch">Branch:</label>
        <select name="branch" id="branch" required>
            <option value="">--Select Campus--</option>
            <option value="AU Main Campus">AU Main Campus</option>
            <option value="AU Pasay">AU Pasay</option>
            <option value="AU Mandaluyong">AU Mandaluy</option>
            <option value="AU Malabon">AU Malabon</option>
            <option value="AU Pasig">AU Pasig</option>
        </select>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" required>

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="Working">Working</option>
            <option value="On-Repair">On-Repair</option>
            <option value="Retired">Retired</option>
        </select>

        <input type="submit" name="btnadd" value="Add Equipment">
        <a href="equipments.php" class="btn-back">⬅ Back to Equipments Dashboard</a>
    </form>
</body>
</html>
