<?php
include("config.php"); 
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username   = $_POST['txtusername'];
    $password   = $_POST['txtpassword'];
    $usertype   = $_POST['txtusertype'];
    $createdby  = $_SESSION['username'] ?? "system";
    $datecreated = date("Y-m-d H:i:s");

    
    $sql = "INSERT INTO tblaccounts (username, password, usertype, status, createdby, datecreated)
            VALUES (?, ?, ?, 'Active', ?, ?)";
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("sssss", $username, $password, $usertype, $createdby, $datecreated);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='create-account.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
