<?php

include("../db/db.php");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_POST["username_or_email"])) {
    header("Location: /forget/index.php?error=empty_form");
    exit();
}

$usernameOrEmail = trim($_POST["username_or_email"]);

if (empty($usernameOrEmail)) {
    header("Location: /forget/index.php?error=empty_form");
    exit();
}

$checkSql = "SELECT id, email FROM users WHERE username='$usernameOrEmail' OR email='$usernameOrEmail'";
$checkResult = mysqli_query($conn, $checkSql);



if (!$checkResult) {
    header("Location: /forget/index.php?error=database_error");
    exit();
}


if ($checkResult->num_rows > 0) {
    $row = mysqli_fetch_assoc($checkResult);
    $userId = $row["id"];
    $email = $row["email"];
    
    $resetCode = bin2hex(random_bytes(16));

    $insertSql = "INSERT INTO forget_password_codes (user_id, code) VALUES ('$userId', '$resetCode')";
    $insertResult = mysqli_query($conn, $insertSql);

    if (!$insertResult) {
        header("Location: /forget/index.php?error=database_error");
        exit();
    }

    $subject = "Password Reset Request";
    $message = "Hello,\n\nUse the code to reset your password:\n\n$resetCode";
    $headers = "From: no-reply@web.0xx.com";

    // TODO: make the mail function work
    if (mail($email, $subject, $message, $headers)) {
        header("Location: /login/index.php");
        exit();
    } else {
        header("Location: /forget/index.php?error=email_failed");
        exit();
    }
} else {
    header("Location: /login/index.php");
    exit();
}