<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-do login</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/login/style.css">
</head>

<body>
    <div class="toggleUIModeContainer container" onclick="toggleUIMode()">
        <img width="25px" class="toggleUIMode" src="/asset/half-moon.svg" alt="UI toggle">
    </div>

    <div class="container">
        <form class="form-box" method="POST" action="login.php">
            <div class="title">
                <strong>To-Do</strong>
            </div>
            <label for="username">Username/Email</label>
            <input type="text" id="username" name="username" class="textfield" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="textfield" required>
            <p id="error" class="errorfield"></p>

            <button type="submit" class="btn">Login</button>
        </form>
    </div>

    <div class="footer-container">
        <a href="/register" class="link">Create Account</a>
        <a href="/forget" class="link right">Forget Password</a>
    </div>

    <script src="/script.js"></script>
    <script src="script.js"></script>
</body>

</html>