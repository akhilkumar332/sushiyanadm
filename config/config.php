<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db105950";
$port = 3306;

$filiale = "neukoelln";

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>