<?php

session_start();

if(isset($_POST["id"]) && isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == TRUE && isset($_POST["up"])){

  $id = $_POST["id"];
  $userid = $_SESSION["id"];
  $up = $_POST["up"] == 1 ? 1 : -1;

  include "sql.php";

  $stmt = $conn->prepare("DELETE FROM votes WHERE wallid=? AND userid=?;");
  $stmt->bind_param("ii", $id, $userid);
  $stmt->execute();

  $stmt = $conn->prepare("INSERT INTO votes VALUES(?, ?, ?);");
  $stmt->bind_param("iii", $userid, $id, $up);
  $stmt->execute();

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

  echo $sum;

}


 ?>
