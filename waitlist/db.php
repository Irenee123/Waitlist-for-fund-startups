<?php
$servername = "localhost"; // Your MySQL server (use 'localhost' if hosted locally)
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "fund_startups"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
