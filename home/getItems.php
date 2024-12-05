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

$token = $_SESSION["token"];
$tokenSql = "SELECT id FROM users WHERE token = '$token'";
$tokenResult = mysqli_query($conn, $tokenSql);


if ($tokenResult->num_rows != 1) {
    http_response_code(401);
    exit();
}

$userRow = mysqli_fetch_assoc($tokenResult);
$userId = $userRow['id'];

if (!isset($_GET['parent'])) {
    http_response_code(400);
    exit();
}

$parentId = intval($_GET['parent']);

$itemSql = "SELECT id, title, description, weight, checked, assign_id 
            FROM items 
            WHERE parent_id = $parentId AND user_id = $userId";

$itemResult = mysqli_query($conn, $itemSql);

$userListSql = "SELECT id, username FROM users";
$userListResult = mysqli_query($conn, $userListSql);
$users = [];
if ($userListResult->num_rows > 0) {
    while ($userRow = mysqli_fetch_assoc($userListResult)) {
        $users[] = $userRow;
    }
}

if ($itemResult->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($itemResult)) {
        echo '
        <div style="display: flex; opacity:' . ($row['checked'] ? '0.3' : '1') .'  ">
            <p class="textfield">' . $row['title'] . '</p>
            <select class="textfield" style="width:30%; margin-left: 10px;" id="assign-' . $row['id'] . '" onchange="assignTask(' . $row['id'] . ')">
                <option value="">Unassigned</option>';
        
        foreach ($users as $user) {
            echo '<option value="' . $user['id'] . '" ' . ($row['assign_id'] == $user['id'] ? 'selected' : '') . '>' . $user['username'] . '</option>';
        }
        
        echo '</select>
            <p class="textfield" style="width: 20%; margin-left: 10px;">' . $row['weight'] . '</p>
            <input type="checkbox" id="checkbox-' . $row['id'] . '" ' . 'onchange="toggleSubItem(\'checkbox-' . $row['id'] . '\')" ' . ($row['checked'] ? 'checked' : "") . '>
        </div>
        ' . (!empty($row['description']) ? '<p class="textfield">' . $row['description'] . '</p>' : '')
        . '<span style="margin-bottom:5px"></span>';
    }
}