<?php

include("../db/db.php");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();
if (!isset($_SESSION['token'])) {
    header("Location: /login/index.php");
    exit();
}


$token = $_SESSION["token"];
$tokenSql = "SELECT id FROM users WHERE token = '$token'";
$tokenResult = mysqli_query($conn, $tokenSql);


if ($tokenResult->num_rows !== 1) {
    header("Location: /login/index.php");
    exit();
}

$userRow = mysqli_fetch_assoc($tokenResult);
$userId = $userRow['id'];


if (!isset($_POST["title"]) || !isset($_POST["icon_uri"])) {
    header("Location: /home/index.php");
    exit();
}

$title = trim($_POST["title"]);
$icon_uri = trim(string: $_POST["icon_uri"]);


if (empty($title) || empty($icon_uri)) {
    header(header: "Location: /home/index.php");
    exit();
}


if (strlen($title) > 32 || strlen($icon_uri) > 32) {
    header(header: "Location: /home/index.php");
    exit();
}

$insertSql = "INSERT INTO todo_lists (user_id, title, icon_uri) VALUES ('$userId', '$title', '$icon_uri')";
mysqli_query($conn, $insertSql);
header(header: "Location: /home/index.php");
exit();

