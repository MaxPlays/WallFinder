<?php

  session_start();

  if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == TRUE){

    $id = $_SESSION["id"];
    if(isset($_POST["title"])){
      $title = $_POST["title"];
      $lat = (double) $_POST["lat"];
      $lng = (double) $_POST["lng"];
      $description = "";

      if(isset($_POST["description"])){
        $description = $_POST["description"];
      }

      if(strlen($title) < 1 || strlen($title) > 40){
        die("The title must be between 1 and 40 characters long.");
      }
      if(strlen($description) > 1000){
        die("The description can only be 1000 characters long.");
      }

      if(!is_double($lat) || !is_double($lng)){
        die("The coordinates are in an invalid format.");
      }

      $time = round(microtime(true) * 1000);

      $votes = 0;

      include "sql.php";

      $stmt = $conn->prepare("INSERT INTO walls(title, description, lat, lng, time, userid, votes) VALUES(?, ?, ?, ?, ?, ?, ?);");
      $stmt->bind_param("ssddiii", $title, $description, $lat, $lng, $time, $id, $votes);
      $stmt->execute();
      $stmt->close();

      echo("Success");

    }else{
      die("You must set a title.");
    }

  }else{
    die("You are not logged in.");
  }

 ?>
