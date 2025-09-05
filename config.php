<?php
//define database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "itc127assessment";

//attempt to connect
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
   
}
//set time zone
date_default_timezone_set('Asia/Manila');
?>