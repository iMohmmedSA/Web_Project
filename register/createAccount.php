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
    $passwordHASH = password_hash($password,60);

    if (empty($username) || empty($email) || empty($password)) {  
        header('Location: /register/index.php?error=empty_form');
        exit();
    }
    
    // Check if user with the same email and username exist or not
    $checkSql = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        header('Location: /register/index.php?error=user_exist');
        exit();
    }

    // Insert the new account
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";  
    if ($conn->query($sql) === TRUE) {
        header('Location: ../login/login.php');
        exit();
    } else {
        header('Location: /register/index.php?error=database_error');
    }
    $conn->close();
?>