<?php
$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "project_db"; 
$port = 3306; // Aapki custom port

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
// Connection successful
?>