<?php

include("../db/db.php");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();
if (!isset($_SESSION['token'])) {
    http_response_code(401);
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['assign'])) {
    http_response_code(400);
    exit();
}

$taskId = intval($_GET['id']);
$assignId = intval($_GET['assign']);

$token = $_SESSION['token'];
$tokenSql = "SELECT id FROM users WHERE token = '$token'";
$tokenResult = mysqli_query($conn, $tokenSql);

if ($tokenResult->num_rows !== 1) {
    http_response_code(401);
    exit();
}

$userRow = mysqli_fetch_assoc($tokenResult);
$userId = $userRow['id'];

$taskSql = "SELECT id FROM items WHERE id = '$taskId' AND user_id = '$userId'";
$taskResult = mysqli_query($conn, $taskSql);

if ($taskResult->num_rows !== 1) {
    http_response_code(403);
    exit();
}

$updateSql = "UPDATE items SET assign_id = " . ($assignId > 0 ? "'$assignId'" : "NULL") . " WHERE id = '$taskId'";
if (!mysqli_query($conn, $updateSql)) {
    http_response_code(500);
    exit();
}

