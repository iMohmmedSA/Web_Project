<?php 

// if there user already loged in then redirect them to the home page

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/register/style.css">
</head>

<body>
    <div class="toggleUIModeContainer container" onclick="toggleUIMode()">
        <img width="25px" class="toggleUIMode" src="/asset/half-moon.svg" alt="UI toggle">
    </div>

    <div class="container">
        <form class="form-box" method="POST" action="createAccount.php">
            <div class="title">
                <strong>To-Do</strong>
            </div>

            <label for="username">Username</label>
            <input type="text" name="username" class="textfield" required>

            <label for="email">Email</label>
            <input type="email" name="email" class="textfield" required>

            <label for="pwd">Password</label>
            <input type="password" name="password" class="textfield" required>

            <button name="submit" class="btn">
                Create a account
            </button>
        </form>
    </div>

    <div>
        <a href="/login" class="link">Log in</a>
    </div>

    <script src="/script.js"></script>
</body>

</html>