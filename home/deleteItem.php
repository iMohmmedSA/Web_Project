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

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: /home/index.php");
    exit();
}

$itemId = intval($_GET["id"]);

if ($itemId <= 0) {
    header("Location: /home/index.php");
    exit();
}

$itemSql = "SELECT id FROM items WHERE id = '$itemId' AND user_id = '$userId'";
$itemResult = mysqli_query($conn, $itemSql);

if ($itemResult->num_rows !== 1) {
    header("Location: /home/index.php");
    exit();
}

$deleteSql = "DELETE FROM items WHERE id = '$itemId' AND user_id = '$userId'";
mysqli_query($conn, $deleteSql);

header("Location: /home/index.php");
exit();