<?php
$servername = "localhost";
$username = "master";
$password = "1234"; // Tu contraseña de MySQL
$dbname = "restaurant_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully";
}
?>
