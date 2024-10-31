<?php
// Database credentials
$servername = "localhost";
$username = "root";   
$password = "1234";       
$dbname = "lab_management";  

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>