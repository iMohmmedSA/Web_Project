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

error_log(message: "Item checked: $itemId, $checked");

if ($tokenResult->num_rows !== 1) {
    header("Location: /login/index.php");
    exit();
}

$userRow = mysqli_fetch_assoc($tokenResult);
$userId = $userRow['id'];

if (!isset($_GET["id"]) || !isset($_GET["checked"])) {
    http_response_code(400);
    exit();
}


$itemId = intval($_GET["id"]);
$checked = $_GET["checked"] === 'true' ? 1 : 0;

$itemCheckSql = "SELECT id FROM items WHERE id = '$itemId' AND user_id = '$userId'";
$itemCheckResult = mysqli_query($conn, $itemCheckSql);

if ($itemCheckResult->num_rows !== 1) {
    http_response_code(403);
    exit();
}

$updateSql = "UPDATE items SET checked = '$checked' WHERE id = '$itemId'";

if (mysqli_query($conn, $updateSql)) {
    http_response_code(200);
} else {
    http_response_code(500);
}

