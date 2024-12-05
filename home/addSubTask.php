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

if (!isset($_POST["title"]) || !isset($_POST["weight"]) || !isset($_POST["item_id"]) || !isset($_POST["list_id"])) {
    header("Location: /home/index.php");
    exit();
}
$title = trim($_POST["title"]);
$weight = intval($_POST["weight"]);
$item_id = intval($_POST["item_id"]);
$list_id = intval($_POST["list_id"]);

if (empty($title) || $weight <= 0) {
    header("Location: /home/index.php");
    exit();
}

if (strlen($title) > 32) {
    header("Location: /home/index.php");
    exit();
}

$listSql = "SELECT id FROM todo_lists WHERE id = '$list_id' AND user_id = '$userId'";
$listSqlResult = mysqli_query($conn, $listSql);

if ($listSqlResult->num_rows !== 1) {
    header("Location: /home/index.php");
    exit();
}

$itemSql = "SELECT id FROM items WHERE id = '$item_id' AND list_id = '$list_id' AND user_id = '$userId'";
$itemSqlResult = mysqli_query($conn, $itemSql);

if ($itemSqlResult->num_rows !== 1) {
    header("Location: /home/index.php");
    exit();
}

$insertSql = "INSERT INTO items (list_id, user_id, title, weight, description, priority, date, time, parent_id) 
              VALUES ('$list_id', '$userId', '$title', '$weight', '', NULL, NULL, NULL, '$item_id')";
mysqli_query($conn, $insertSql);

header("Location: /home/index.php");
exit();

?>
