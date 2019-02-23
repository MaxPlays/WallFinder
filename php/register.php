<?php

  include "sql.php";

  if(isset($_POST["email"]) && isset($_POST["user"]) && isset($_POST["pass"])){

    $email = strtolower($_POST["email"]);
    $user = $_POST["user"];
    $pass = $_POST["pass"];

    if(preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $email) == 0){
      die("The email address you entered is not valid");
    }
    if(strlen($user) < 3 || strlen($user) > 20){
      die("The username must be between 3 and 20 characters");
    }
    if(preg_match('/^[A-Za-z0-9]+$/', $user) == 0){
      die("The username can only contain letters and numbers");
    }
    if(strlen($pass) < 6 || strlen($pass) > 25){
      die("The password must be between 6 and 25 characters long");
    }
    if(strlen($email) > 200){
      die("The email you entered is too long");
    }

    $stmt = $conn->prepare("SELECT LOWER(user) FROM users WHERE user=?;");
    $stmt->bind_param("s", strtolower($user));
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0){
      die("The username you entered is already in use");
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT LOWER(email) FROM users WHERE email=?;");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0){
      die("The email you entered is already in use");
    }
    $stmt->close();

    $pass = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users(user, email, password) VALUES(?, ?, ?);");
    $stmt->bind_param("sss", $user, $email, $pass);
    $stmt->execute();
    $stmt->close();

    echo "Success";

  }

 ?>
