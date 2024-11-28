<?php
    include '../db/db.php';
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (!(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']))) {
        header('Location: /register/index.php?error=empty_form');
        exit();
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $passwordHASH = password_hash($password, PASSWORD_DEFAULT);

    if (empty($username) || empty($email) || empty($password)) {  
        header('Location: /register/index.php?error=empty_form');
        exit();
    }
    
    // Check if user with the same email and username exist or not
    $checkSql = "SELECT * FROM users WHERE (username='$username' OR email='$email') AND email_confirmed=1 AND status='active'";
    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        header('Location: /register/index.php?error=user_exist');
        exit();
    }

    $token = bin2hex(random_bytes(50));

    // Insert the new account
    $sql = "INSERT INTO users (username, email, password, token) VALUES ('$username', '$email', '$passwordHASH', '$token')";  
    if ($conn->query($sql) === TRUE) {
        // send Email code
        // TODO

        // genrate 6 random letter
        $verificationCode = substr(str_shuffle(string: '0123456789'), 0, length: 6);
        // Insert the verification code into the activation_codes table
        $userId = $conn->insert_id;
        $insertCodeSql = "INSERT INTO activation_codes (user_id, code) VALUES ('$userId', '$verificationCode')";
        if ($conn->query($insertCodeSql) === TRUE) {
            header("Location: /register/verify/index.php?id=$userId");
        } else {
            header('Location: /register/index.php?error=database_error');
            exit();
        }
        
        exit();
    } else {
        header('Location: /register/index.php?error=database_error');
    }
    $conn->close();
?>