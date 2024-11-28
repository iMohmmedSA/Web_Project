<?php
include("../db/db.php");

if (isset($_POST["reset_code"])) {
    if (!isset($_POST["new_password"])) {
        header("Location: /forget/reset.php?error=empty_form");
        exit();
    }

    $resetCode = trim($_POST["reset_code"]);
    $newPassword = trim($_POST["new_password"]);

    if (empty($resetCode) || empty($newPassword)) {
        header("Location: /forget/reset.php?error=empty_form");
        exit();
    }

    $checkSql = "SELECT user_id FROM forget_password_codes WHERE code='$resetCode' AND created_at >= NOW() - INTERVAL 24 HOUR";
    $checkResult = mysqli_query($conn, $checkSql);

    if (!$checkResult) {
        header("Location: /forget/reset.php?error=database_error");
        exit();
    }

    if ($checkResult->num_rows > 0) {
        $row = mysqli_fetch_assoc($checkResult);
        $userId = $row["user_id"];
        
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $updateSql = "UPDATE users SET password='$hashedPassword' WHERE id='$userId'";
        $updateResult = mysqli_query($conn, $updateSql);

        if (!$updateResult) {
            header("Location: /forget/reset.php?error=database_error");
            exit();
        }

        $removeRecord = "DELETE FROM forget_password_codes WHERE code='$resetCode'";
        $removeRecordResult = mysqli_query($conn, $removeRecord);

        if (!$removeRecordResult) {
            header("Location: /forget/reset.php?error=database_error");
            exit();
        }

        header("Location: /login/index.php?message=password_reset_success");
        exit();
    } else {
        header("Location: /forget/reset.php?error=invalid_code");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/forget/style.css">
</head>

<body>
    <div class="toggleUIModeContainer container" onclick="toggleUIMode()">
        <img width="25px" class="toggleUIMode" src="/asset/half-moon.svg" alt="UI toggle">
    </div>

    <div class="container">
        <form class="form-box" method="POST" onsubmit="return validatePasswords()">
            <div class="title">
                <strong>Reset Password</strong>
            </div>
            <label for="reset_code">Reset Code</label>
            <input type="text" id="reset_code" name="reset_code" class="textfield" required>

            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" class="textfield" required>

            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="textfield" required>


            <p id="error" class="errorfield"></p>

            <button class="btn" onsubmit="return validatePasswords()">Reset Password</button>
        </form>
    </div>

    <div class="footer-container">
        <a href="/register" class="link">Create account </a>
        <a href="/login" class="link right">Login</a>
    </div>

    <script src="/script.js"></script>
    <script src="script.js"></script>
    <script>
        function validatePasswords() {
            console.log("Validating passwords");

            const password = document.getElementById("new_password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const errorMessage = document.getElementById("error");

            if (password !== confirmPassword) {
                errorMessage.style.display = "block";
                errorMessage.innerText = "Passwords do not match";
                return false;
            }

            errorMessage.style.display = "none";
            return true;
        }
    </script>
</body>

</html>