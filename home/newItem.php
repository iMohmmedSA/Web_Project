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

if (!isset($_POST["title"])) {
    header("Location: /home/index.php");
    exit();
}

$title = trim($_POST["title"]);
$date = isset($_POST["date"]) ? trim($_POST["date"]) : null;
$time = isset($_POST["time"]) ? trim($_POST["time"]) : null;
$priority = isset($_POST["priority"]) ? trim($_POST["priority"]) : null;
$list_id = isset($_POST["list_id"]) && $_POST["list_id"] !== "none" ? trim($_POST["list_id"]) : null;

if (empty($title)) {
    header("Location: /home/index.php");
    exit();
}

if (strlen($title) > 32) {
    header("Location: /home/index.php");
    exit();
}

switch ($priority) {
    case "":
    case 'none':
    case 'low':
    case 'medium':
    case 'high':
        break;    
    default:
        header("Location: /home/index.php");
        exit();
}


if ($list_id === null) {
    $defaultListSql = "SELECT id FROM todo_lists WHERE user_id = '$userId' AND title = 'Default'";
    $defaultListResult = mysqli_query($conn, $defaultListSql);

    if ($defaultListResult->num_rows === 0) {
        $createDefaultListSql = "INSERT INTO todo_lists (user_id, title, icon_uri) VALUES ('$userId', 'Default', '/asset/icons/default.svg')";
        mysqli_query($conn, $createDefaultListSql);
        $list_id = $conn->insert_id;
    } else {
        $defaultListRow = mysqli_fetch_assoc($defaultListResult);
        $list_id = $defaultListRow['id'];
    }
}

$dateValue = empty($date) ? "NULL" : "'$date'";
$timeValue = empty($time) ? "NULL" : "'$time'";


$insertSql = "INSERT INTO items (list_id, user_id, title, priority, date, time) 
              VALUES ('$list_id', '$userId', '$title', 
              " . ($priority !== null ? "'$priority'" : "NULL") . ", 
                  $dateValue, 
                  $timeValue)";
mysqli_query($conn, $insertSql);

header("Location: /home/index.php");
exit();
