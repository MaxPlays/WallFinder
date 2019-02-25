<?php
  $host = "127.0.0.1";
  $port = 3306;
  $user = "root";
  $password = "";
  $database = "wallfinder";
  $conn = new mysqli($host, $user, $password, $database);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $conn->query("CREATE TABLE IF NOT EXISTS users(userid INT AUTO_INCREMENT PRIMARY KEY, user VARCHAR(20), email TEXT, password VARCHAR(256));");
  $conn->query("CREATE TABLE IF NOT EXISTS walls(wallid INT AUTO_INCREMENT PRIMARY KEY, title TEXT, description TEXT, lat DOUBLE, lng DOUBLE, time LONG, userid INT);");
  $conn->query("CREATE TABLE IF NOT EXISTS tokens(user INT, token VARCHAR(256), PRIMARY KEY(user, token));");
  $conn->query("CREATE TABLE IF NOT EXISTS votes(userid INT, wallid INT, score INT, PRIMARY KEY(userid, wallid));");

 ?>
