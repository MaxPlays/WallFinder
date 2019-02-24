<?php

  if(isset($_POST["id"])){
    $id = $_POST["id"];

    include "sql.php";

    $stmt = $conn->prepare("SELECT title, description FROM walls WHERE wallid=?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
      $stmt->bind_result($title, $description);
      $stmt->fetch();
    }

    $stmt = $conn->prepare("SELECT SUM(score) AS sum FROM votes WHERE wallid=?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
      $stmt->bind_result($sum);
      $stmt->fetch();
    }

    if($sum == null){
      $sum = 0;
    }

    $json = json_encode(array("title" => $title, "description" => $description, "score" => $sum));

    echo $json;
  }

 ?>
