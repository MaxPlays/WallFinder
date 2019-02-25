<?php

  session_start();

  include "sql.php";
  include "config.php";

  if(isset($_POST["user"]) && isset($_POST["pass"]) && isset($_POST["stay"])){

    $user = $_POST["user"];
    $pass = $_POST["pass"];
    $stay = $_POST["stay"];

    $u = strtolower($user);

    $stmt = $conn->prepare("SELECT userid, user, password FROM users WHERE user=?;");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
      $stmt->bind_result($id, $username, $password);
      $stmt->fetch();
      if(password_verify($pass, $password)){
        $_SESSION["loggedin"] = TRUE;
        $_SESSION["user"] = $username;
        $_SESSION["id"] = $id;

        if($stay == "true"){
          $token = hash("sha256", uniqid(rand(), true));

          $stmt = $conn->prepare("INSERT INTO tokens VALUES(?, ?);");
          $stmt->bind_param("is", $id, $token);
          $stmt->execute();

          $cookie = $user . ':' . $token;
          $mac = hash_hmac('sha256', $cookie, $secret_key);
          $cookie .= ':' . $mac;
          echo $cookie;
        }else{
          echo "Success";
        }
      }else{
        die("Incorrect username or password.");
      }

    }else{
      die("Incorrect username or password.");
    }

  }

 ?>
