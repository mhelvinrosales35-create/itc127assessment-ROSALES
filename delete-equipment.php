<?php
require_once "config.php";
include("session-checker.php");

// Check if 'assetnumber' parameter is set
if (isset($_GET['assetnumber'])) {
    $assetNumberToDelete = $_GET['assetnumber']; // Get the asset number from the URL

    // Prepare the SQL statement for deleting the equipment
    $sql = "DELETE FROM tblequipments WHERE assetnumber = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {   // gumamit tayo ng $conn (from config.php)
        mysqli_stmt_bind_param($stmt, "s", $assetNumberToDelete);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt); // Close delete statement

            // Log the deletion action sa tblequipmentslogs
            $sql = "INSERT INTO tblequipmentslogs 
                        (datelog, timelog, action, module, assetnumber, performedby) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                $date = date("d/m/Y");
                $time = date("h:i:sa");
                $action = "Delete";
                $module = "Equipments Management";
                $performedBy = $_SESSION['username'];

                mysqli_stmt_bind_param(
                    $stmt,
                    "ssssss",
                    $date,
                    $time,
                    $action,
                    $module,
                    $assetNumberToDelete,
                    $performedBy
                );
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Redirect back to equipments page
echo "<script>window.location.href = 'equipments.php';</script>";
?>
