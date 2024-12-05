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

if (!isset($_POST["title"]) || !isset($_POST["icon_uri"]) || !isset($_POST["list_id"])) {
    header("Location: /home/index.php");
    exit();
}

$title = trim($_POST["title"]);
$icon_uri = trim($_POST["icon_uri"]);
$list_id = $_POST["list_id"];

if (empty($title) || empty($icon_uri) || $list_id <= 0) {
    header("Location: /home/index.php");
    exit();
}

if (strlen($title) > 32 || strlen($icon_uri) > 32) {
    header("Location: /home/index.php");
    exit();
}

$listSql = "SELECT id FROM todo_lists WHERE id = '$list_id' AND user_id = '$userId'";
$listSqlResult = mysqli_query($conn, $listSql);

if ($listSqlResult->num_rows !== 1) {
    header("Location: /home/index.php");
    exit();
}

$updateSql = "UPDATE todo_lists SET title = '$title', icon_uri = '$icon_uri' WHERE id = '$list_id' AND user_id = '$userId'";
mysqli_query($conn, $updateSql);

header("Location: /home/index.php");
exit();

?>
