<?php 

// if there user already loged in then redirect them to the home page
// TODO

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your account</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/register/verify/style.css">
</head>

<body>
    <div class="toggleUIModeContainer container" onclick="toggleUIMode()">
        <img width="25px" class="toggleUIMode" src="/asset/half-moon.svg" alt="UI toggle">
    </div>
    <div class="container">
        <form class="form-box" method="POST" action="verify.php">
            <div class="title">
                <strong>To-Do</strong>
            </div>
            <input id="userID" name="id" type="hidden">
            <input name="verfiy" type="number" max=999999 style="text-align: center;" placeholder="000 - 000" class="textfield" required/>
            <p class="info-text">Code sent to your email.</p>
            <p id="error" class="errorfield"></p>
            <button class="btn">Verify</button>
        </form>
    </div>

    <script src="/script.js"></script>
    <script src="script.js"></script>
</body>

</html>