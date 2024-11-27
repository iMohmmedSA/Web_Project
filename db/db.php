<?php

$servername = "db";
$username = "todo";
$password = "todo";
$dbname = "todo";

// // Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// // Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>