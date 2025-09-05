<?php
// Start session and check if the user is logged in
require_once "config.php";
include("session-checker.php");

// Initialize the $equipment variable
$equipment = [];

// Check if 'assetnumber' is passed in the URL
if (isset($_GET['assetnumber'])) {
    $assetnumber = $_GET['assetnumber'];  // Use the assetnumber from the URL

    // SQL query to fetch equipment details based on assetnumber
    $sql = "SELECT * FROM tblequipments WHERE assetnumber = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $assetnumber);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $equipment = mysqli_fetch_array($result, MYSQLI_ASSOC);
        } else {
            echo "Equipment not found.";
            exit;
        }
    } else {
        echo "Error preparing the query.";
        exit;
    }
}

// Handle the form submission for updating the equipment
if (isset($_POST['btnupdate'])) {
    $serialnumber = $_POST['serialnumber'];
    $sql_check_serial = "SELECT * FROM tblequipments WHERE serialnumber = ? AND assetnumber != ?";
    if ($check_stmt = mysqli_prepare($conn, $sql_check_serial)) {
        mysqli_stmt_bind_param($check_stmt, "ss", $serialnumber, $_POST['assetnumber']);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "<font color='red'>ERROR: Serial number is already in use.</font>";
        }
    }

    if (empty($error_message)) {
        $sql = "SELECT * FROM tblequipments WHERE assetnumber = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $_POST['assetnumber']);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $old_data = mysqli_fetch_assoc($result);

                $changes_made = false;
                $fields_to_update = [];

                foreach ($_POST as $key => $value) {
                    if ($key !== 'btnupdate' && $key !== 'assetnumber') {
                        if ($old_data[$key] != $value) {
                            $changes_made = true;
                            $fields_to_update[$key] = $value;
                        }
                    }
                }

                if ($changes_made) {
                    $update_sql = "UPDATE tblequipments SET
                        serialnumber = ?, 
                        type = ?, 
                        manufacturer = ?, 
                        yearmodel = ?, 
                        description = ?, 
                        branch = ?, 
                        department = ?, 
                        status = ?
                        WHERE assetnumber = ?";
                    
                    if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                        $serialnumber = $fields_to_update['serialnumber'] ?? $old_data['serialnumber'];
                        $type = $fields_to_update['type'] ?? $old_data['type'];
                        $manufacturer = $fields_to_update['manufacturer'] ?? $old_data['manufacturer'];
                        $yearmodel = $fields_to_update['yearmodel'] ?? $old_data['yearmodel'];
                        $description = $fields_to_update['description'] ?? $old_data['description'];
                        $branch = $fields_to_update['branch'] ?? $old_data['branch'];
                        $department = $fields_to_update['department'] ?? $old_data['department'];
                        $status = $fields_to_update['status'] ?? $old_data['status'];
                        $assetnumber = $_POST['assetnumber'];

                        mysqli_stmt_bind_param(
                            $update_stmt, 
                            "sssssssss", 
                            $serialnumber,
                            $type,
                            $manufacturer,
                            $yearmodel,
                            $description,
                            $branch,
                            $department,
                            $status,
                            $assetnumber
                        );

                        if (mysqli_stmt_execute($update_stmt)) {
                            $log_sql = "INSERT INTO tblequipmentslogs (datelog, timelog, assetnumber, performedby, action, module) 
                                        VALUES (?, ?, ?, ?, ?, ?)";
                            if ($log_stmt = mysqli_prepare($conn, $log_sql)) {
                                $datelog = date('d/M/Y');
                                $timelog = date('h:i:sa');
                                $action = "Update";
                                $module = "Equipments";
                                mysqli_stmt_bind_param($log_stmt, "ssssss", $datelog, $timelog, $_POST['assetnumber'], $_SESSION['username'], $action, $module);
                                mysqli_stmt_execute($log_stmt);
                            }
                            header("location: equipments.php");
                            exit();
                        } else {
                            echo "<font color='red'>Error updating equipment.</font>";
                        }
                    }
                } else {
                    echo "<font color='green'>No changes were made to the equipment data.</font>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Equipment</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #dfefff, #87ceeb);
            overflow-x: hidden;
        }
        h1 {
            text-align: center;
            color: black;
            margin: 20px 0;
        }
        form {
            background: #444c52;
            color: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
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
            border: none;
        }
        input[type="submit"] {
            background: #4CAF50;
            margin-top: 15px;
            padding: 10px;
            color: white;
            cursor: pointer;
        }
        .back-button {
            display: block;
            text-align: center;
            margin: 10px auto;
            background: #2e8b57;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            width: fit-content;
            text-decoration: none;
            font-size: 14px;
        }
        .cloud {
            position: absolute;
            top: 0;
            background: url('cloud.png') repeat-x;
            width: 300%;
            height: 200px;
            animation: moveClouds 60s linear infinite;
            opacity: 0.3;
        }
        @keyframes moveClouds {
            from { transform: translateX(0); }
            to { transform: translateX(-100%); }
        }
    </style>
</head>
<body>
    <div class="cloud"></div>
    <h1>Edit Equipment</h1>
    <?php if (isset($error_message)) echo $error_message; ?>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label>Asset Number:</label>
        <input type="text" name="assetnumber" value="<?php echo htmlspecialchars($equipment['assetnumber'] ?? ''); ?>" readonly required>

        <label>Serial Number:</label>
        <input type="text" name="serialnumber" value="<?php echo htmlspecialchars($equipment['serialnumber'] ?? ''); ?>" required>

        <label>Type:</label>
        <select name="type" required>
            <?php
            $types = ["Monitor", "CPU", "Keyboard", "Mouse", "AVR", "MAC", "Printer", "Projector"];
            foreach ($types as $type) {
                $selected = (isset($equipment['type']) && $equipment['type'] == $type) ? 'selected' : '';
                echo "<option value=\"$type\" $selected>$type</option>";
            }
            ?>
        </select>

        <label>Manufacturer:</label>
        <input type="text" name="manufacturer" value="<?php echo htmlspecialchars($equipment['manufacturer'] ?? ''); ?>" required>

        <label>Year Model:</label>
        <input type="text" name="yearmodel" value="<?php echo htmlspecialchars($equipment['yearmodel'] ?? ''); ?>" required>

        <label>Description:</label>
        <textarea name="description"><?php echo htmlspecialchars($equipment['description'] ?? ''); ?></textarea>

        <label>Branch:</label>
        <select name="branch" required>
            <?php
            $branches = ["AU Main Campus", "AU Pasay", "AU Mandaluyong", "AU Malabon", "AU Pasig"];
            foreach ($branches as $branch) {
                $selected = (isset($equipment['branch']) && $equipment['branch'] == $branch) ? 'selected' : '';
                echo "<option value=\"$branch\" $selected>$branch</option>";
            }
            ?>
        </select>

        <label>Department:</label>
        <input type="text" name="department" value="<?php echo htmlspecialchars($equipment['department'] ?? ''); ?>" required>

        <label>Status:</label>
        <select name="status" required>
            <?php
            $statuses = ["Working", "On-Repair", "Retired"];
            foreach ($statuses as $status) {
                $selected = (isset($equipment['status']) && $equipment['status'] == $status) ? 'selected' : '';
                echo "<option value=\"$status\" $selected>$status</option>";
            }
            ?>
        </select>

        <input type="submit" name="btnupdate" value="Update Equipment">
    </form>

    <a href="equipments.php" class="back-button">⬅ Back to Equipment List</a>
</body>
</html>
