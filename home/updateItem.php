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

if (!isset($_POST["title"]) || !isset($_POST["item_id"]) || !isset($_POST["list_id"])) {
    header("Location: /home/index.php");
    exit();
}

$title = trim($_POST["title"]);
$description = trim($_POST["description"]);
$date = isset($_POST["date"]) ? trim($_POST["date"]) : null;
$time = isset($_POST["time"]) ? trim($_POST["time"]) : null;
$priority = isset($_POST["priority"]) ? trim($_POST["priority"]) : null;
$itemId = intval($_POST["item_id"]);
$listId = intval($_POST["list_id"]);

if (empty($title) || strlen($title) > 32 || strlen($description) > 128) {
    header("Location: /home/index.php");
    exit();
}

$itemSql = "SELECT id FROM items WHERE id = '$itemId' AND list_id = '$listId' AND user_id = '$userId'";
$itemResult = mysqli_query($conn, $itemSql);

if ($itemResult->num_rows !== 1) {
    header("Location: /home/index.php");
    exit();
}

if ($priority == "none") {
    $priority = null;
}


$updateSql = "UPDATE items 
              SET title = '$title', description = '$description', date = " . ($date ? "'$date'" : "NULL") . ", 
                  time = " . ($time ? "'$time'" : "NULL") . ", priority = " . ($priority ? "'$priority'" : "NULL") . " 
              WHERE id = '$itemId' AND user_id = '$userId'";

error_log($updateSql);
mysqli_query($conn, $updateSql);

header("Location: /home/index.php");
exit();
