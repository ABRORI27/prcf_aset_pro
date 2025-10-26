<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully <br>";
// Create database
$sql = "CREATE DATABASE db_prcf";
if ($conn->query($sql) === TRUE) {
  echo "Database db_prcf created successfully";
} else {
  echo "Error creating database db_prcf: " . $conn->error;
}

// Close connection
$conn->close();
?>