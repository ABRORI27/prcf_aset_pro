<?php
$servername  = "localhost";
$username = "root";
$password = "";
$dbname = "db_prcf";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (headers_sent($file, $line)) {
    echo "Header sudah dikirim di file $file baris $line";
    exit;
}

?>
