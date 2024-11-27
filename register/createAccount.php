<?php
    include '../db/db.php';
    
    if (!$conn) {
        // [EE Here]
        die("Connection failed: " . mysqli_connect_error());
    }

    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password = md5($password);
    } else {
        header('Location: register.php');
        exit();
    }

    $username = trim($username);
    $email = trim($email);
    $password = trim($password);

    if (empty($username) || empty($email) || empty($password)) {  
        header('Location: register.php');
        exit();
    }
    
    $checkSql = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        // Redirect to register with the error message
        // TODO
        echo "Username or email already exists.";
        exit();
    }

    // Insert the new account
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";  if ($conn->query($sql) === TRUE) {
        header('Location: ../login/login.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
?>