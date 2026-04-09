<?php
$host = "localhost";
$port = "3307";
$user = "root";
$password = "170524"; // tu contraseña si tienes
$dbname = "baseapp";

$conn = new mysqli($host, $user, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>