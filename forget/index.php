<?php 

// if there user already loged in then redirect them to the home page
// TODO

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/forget/style.css">
</head>

<body>
    <div class="toggleUIModeContainer container" onclick="toggleUIMode()">
        <img width="25px" class="toggleUIMode" src="/asset/half-moon.svg" alt="UI toggle">
    </div>


    <div class="container">
        <form class="form-box" method="POST" action="forget.php">
            <div class="title">
                <strong>To-Do</strong>
            </div>
            <label>Username/Email</label>
            <input type="text" name="username_or_email" class="textfield" required>
            <button class="btn">Send reset link</button>
        </form>

    </div>

    <div class="footer-container">
        <a href="/register" class="link">Create account </a>
        <a href="/login" class="link right">Login</a>
    </div>

    <script src="/script.js"></script>
</body>

</html>