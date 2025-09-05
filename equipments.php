<?php
require_once "config.php";
include("session-checker.php");

if ($_SESSION['usertype'] !== 'Administrator' && $_SESSION['usertype'] !== 'Technical') {
    header("Location: dashboard.php");
    exit;
}

// Initialize search variables
$assetnumber = '';
$serialnumber = '';
$type = '';
$department = '';

// Check if search fields are set
if(isset($_POST['assetnumber'])) {
    $assetnumber = $_POST['assetnumber'];
}
if(isset($_POST['serialnumber'])) {
    $serialnumber = $_POST['serialnumber'];
}
if(isset($_POST['type'])) {
    $type = $_POST['type'];
}
if(isset($_POST['department'])) {
    $department = $_POST['department'];
}

$sql = "SELECT * FROM tblequipments WHERE 
            assetnumber LIKE ? AND 
            serialnumber LIKE ? AND 
            type LIKE ? AND 
            department LIKE ? 
        ORDER BY datecreated ASC";


$stmt = mysqli_prepare($conn, $sql);

// Add wildcard (%) for partial matching
$assetTerm = "%" . $assetnumber . "%";
$serialTerm = "%" . $serialnumber . "%";
$typeTerm = "%" . $type . "%";
$departmentTerm = "%" . $department . "%";

// Bind parameters
mysqli_stmt_bind_param($stmt, 'ssss', $assetTerm, $serialTerm, $typeTerm, $departmentTerm);

// Execute query
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Equipments - Equipment Management System</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background: url('building.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .box {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.5);
            width: 95%;
            max-width: 1100px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .search-form {
            margin-bottom: 20px;
        }

        .search-input {
            padding: 8px;
            margin: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            width: 200px;
        }

        .search-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            background-color: #333;
            color: white;
            cursor: pointer;
        }

        .search-btn:hover {
            background-color: #555;
        }

        .dashboard-buttons {
            margin-bottom: 20px;
        }

        .dashboard-buttons button {
            margin: 5px;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        .dashboard-buttons button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #333;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        tr:hover {
            background-color: #e2e2e2;
        }

        .edit-link, .delete-link {
            margin: 0 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .edit-link {
            color: green;
        }

        .delete-link {
            color: red;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 300px;
        }

        .modal-content button {
            margin: 10px;
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .modal-content button:first-child {
            background-color: red;
            color: #fff;
        }

        .modal-content button:last-child {
            background-color: #ccc;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Equipments</h1>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="search-form">
            <input type="text" name="assetnumber" placeholder="Search by Asset Number" value="<?php echo htmlspecialchars($assetnumber); ?>" class="search-input">
            <input type="text" name="serialnumber" placeholder="Search by Serial Number" value="<?php echo htmlspecialchars($serialnumber); ?>" class="search-input">
            <input type="text" name="type" placeholder="Search by Type" value="<?php echo htmlspecialchars($type); ?>" class="search-input">
            <input type="text" name="department" placeholder="Search by Department" value="<?php echo htmlspecialchars($department); ?>" class="search-input">
            <input type="submit" value="Search" class="search-btn">
        </form>

        <div class="dashboard-buttons">
            <button onclick="location.href='add_equipment.php'">Add Equipment</button>
            <button onclick="location.href='dashboard.php'">Back to Dashboard</button>
        </div>

        <h2>Equipment List</h2>
        <table>
            <tr>
                <th>Asset Number</th>
                <th>Serial Number</th>
                <th>Type</th>
                <th>Department</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['assetnumber']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['serialnumber']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>";
                    echo "<a href='update-equipments.php?assetnumber=" . urlencode($row['assetnumber']) . "' class='edit-link'>Update</a>";
                    echo "<a href='#' onclick='showModal(\"" . $row['assetnumber'] . "\")' class='delete-link'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No equipment found.</td></tr>";
            }
            ?>
        </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to delete this equipment?</p>
            <button onclick="confirmDelete()">Yes</button>
            <button onclick="cancelDelete()">No</button>
            <input type="hidden" id="deleteAssetNumber" />
        </div>
    </div>

    <script>
    function showModal(assetnumber) {
        document.getElementById("confirmationModal").style.display = "flex";
        document.getElementById("deleteAssetNumber").value = assetnumber;
    }

    function confirmDelete() {
        var assetnumber = document.getElementById("deleteAssetNumber").value;
        window.location.href = "delete-equipment.php?assetnumber=" + assetnumber;
    }

    function cancelDelete() {
        document.getElementById("confirmationModal").style.display = "none";
    }
    </script>
</body>
</html>
