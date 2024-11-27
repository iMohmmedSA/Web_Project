<?php
    include '../../db/db.php';
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (!(isset($_POST['verfiy']) && isset($_POST['id']))) {
        header("Location: /register/verify/index.php?id=$id&error=empty_form");
        exit();
    }

    $verfiyCode = trim($_POST['verfiy']);
    $id = trim($_POST['id']);

    if (empty($verfiyCode) || empty($id)) {  
        header("Location: /register/verify/index.php?id=$id&error=empty_form");
        exit();
    }

    $sqlVerifyCode = "SELECT * FROM activation_codes WHERE user_id = $id AND code = $verfiyCode";
    $result = $conn->query($sqlVerifyCode);

    if ($result->num_rows > 0) {
        // Activate the account
        $sqlActivate = "UPDATE users 
        SET status = 'active', email_confirmed = TRUE 
        WHERE id = $id";

        $result = $conn->query($sqlActivate);
        if ($result) {
            header('Location: /login/index.php');

            // Delete the activation code
            $sqlDelete = "DELETE FROM activation_codes WHERE user_id = $id";
            $conn->query($sqlDelete);
        } else {
            header("Location: /register/verify/index.php?id=$id&error=activation_failed");
        }
    } else {
        header("Location: /register/verify/index.php?id=$id&error=invalid_code");
    }
    

    $conn->close();
?>