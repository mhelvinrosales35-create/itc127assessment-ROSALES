<?php
require_once "config.php";
include("session-checker.php");

if (isset($_GET['username'])) {
    $usernameToDelete = $_GET['username'];

    $sql = "DELETE FROM tblaccounts WHERE username = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $usernameToDelete);
        if (mysqli_stmt_execute($stmt)) {    
            // Log deletion
            $sql = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                $date = date("d/m/Y");
                $time = date("h:i:sa");
                $action = "Delete";
                $module = "Accounts Management";
                mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $usernameToDelete, $_SESSION['username']);
                mysqli_stmt_execute($stmt);
            }
            $message = "User account deleted successfully!";
        } else {
            $message = "Error deleting the account.";
        }
    }
} else {
    $message = "No username provided.";
}

echo "<script>
    alert('$message');
    window.location.href = 'accounts-management.php';
</script>";
?>
