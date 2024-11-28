<?php

include("../db/db.php");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!(isset($_POST["username"]) && isset($_POST["password"]))) {
    header("Location: /login/index.php?error=empty_form");
    exit();
}

$username = trim($_POST["username"]);
$password = trim($_POST["password"]);
$passwordHASH = password_hash($password, PASSWORD_DEFAULT);

if (empty($username) || empty($password)) {
    header("Location: /login/index.php?error=empty_form");
    exit();
}

$checkSql = "SELECT * FROM users WHERE username='$username' OR email='$username'";
$checkResult = mysqli_query($conn, $checkSql);

if (!$checkResult) {
    header("Location: /login/index.php?error=database_error");
    exit();
}

if ($checkResult->num_rows > 0) {
    $row = mysqli_fetch_assoc($checkResult);
    if (password_verify($password, $row["password"])) {
        session_start();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION["token"] = $row["token"];
        header("Location: /home/index.php");
        exit();
    } else {
        header("Location: /login/index.php?error=invalid_credentials");
        exit();
    }
} else {
    header("Location: /login/index.php?error=invalid_credentials");
    exit();
}